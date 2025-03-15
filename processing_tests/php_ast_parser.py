#!/usr/bin/env python3
"""
PHP AST Parser for FORAI.

This script implements an AST-based analyzer for PHP files.
It uses the php-ast module when available, or falls back to
a subprocess call to the php binary with ast generation.
"""

import os
import sys
import json
import subprocess
import tempfile
import re
from typing import Dict, List, Optional, Any, Union

class PHPAstAnalyzer:
    """AST-based analyzer for PHP files."""
    
    def __init__(self, symbol_registry):
        """Initialize the PHP AST analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a PHP file using AST.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Get AST using PHP subprocess
        ast_data = self._get_ast_for_file(file_path)
        
        # Parse AST to extract definitions, imports, exports
        definitions = []
        imports = []
        namespace = None
        
        # Extract namespace if present
        if 'namespace' in ast_data:
            namespace = ast_data['namespace']
        
        # Extract class definitions
        if 'classes' in ast_data:
            for class_info in ast_data['classes']:
                class_name = class_info['name']
                
                # Get symbol ID
                symbol_id = self.registry.get_symbol_id(file_id, class_name, 'class')
                
                # Create class definition
                class_def = {
                    'symbol_id': symbol_id,
                    'name': class_name,
                    'type': 'class'
                }
                
                # Add parent class if exists
                if 'extends' in class_info and class_info['extends']:
                    class_def['parents'] = [class_info['extends']]
                
                # Add interfaces if implemented
                if 'implements' in class_info and class_info['implements']:
                    class_def['interfaces'] = class_info['implements']
                
                definitions.append(class_def)
                
                # Add class methods
                if 'methods' in class_info:
                    for method_info in class_info['methods']:
                        method_name = method_info['name']
                        
                        # Get symbol ID
                        method_symbol_id = self.registry.get_symbol_id(file_id, method_name, 'function')
                        
                        # Create method definition
                        method_def = {
                            'symbol_id': method_symbol_id,
                            'name': method_name,
                            'type': 'method',
                            'parent': symbol_id
                        }
                        
                        definitions.append(method_def)
        
        # Extract function definitions
        if 'functions' in ast_data:
            for func_info in ast_data['functions']:
                func_name = func_info['name']
                
                # Get symbol ID
                symbol_id = self.registry.get_symbol_id(file_id, func_name, 'function')
                
                # Create function definition
                func_def = {
                    'symbol_id': symbol_id,
                    'name': func_name,
                    'type': 'function'
                }
                
                definitions.append(func_def)
        
        # Extract imports (use statements)
        if 'imports' in ast_data:
            for import_info in ast_data['imports']:
                import_path = import_info['path']
                
                # For now, set as unknown
                imports.append({
                    'file_id': 'unknown',
                    'symbol_id': '*'
                })
        
        # Extract includes/requires
        if 'includes' in ast_data:
            for include_info in ast_data['includes']:
                include_path = include_info['path']
                
                # For now, set as unknown
                imports.append({
                    'file_id': 'unknown',
                    'symbol_id': '*'
                })
        
        # For PHP, all public classes and functions are exported
        exports = [def_item['symbol_id'] for def_item in definitions 
                  if not def_item['name'].startswith('_')]
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
    
    def _get_ast_for_file(self, file_path: str) -> Dict[str, Any]:
        """Generate and parse PHP AST for a file.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            Dictionary with AST data
        """
        # Read the content of the file
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Create a temporary file for the PHP AST generation script
        with tempfile.NamedTemporaryFile(suffix='.php', delete=False) as temp_file:
            temp_file_path = temp_file.name
            
            # Write a simple PHP script that reads the file directly instead of embedding content
            ast_script = '''<?php
// Read the file content directly
$code = file_get_contents("''' + file_path + '''");

// Create a simple AST representation
$ast = [];

// Try to parse the file
$tokens = token_get_all($code);

// Track state
$currentNamespace = '';
$currentClass = null;
$currentUse = null;
$classes = [];
$functions = [];
$imports = [];
$includes = [];

// Process tokens
foreach ($tokens as $token) {
    if (is_array($token)) {
        list($id, $text) = $token;
        
        // Namespace detection
        if ($id === T_NAMESPACE) {
            $i = array_search($token, $tokens) + 1;
            $namespace = '';
            while (isset($tokens[$i]) && $tokens[$i] !== ';') {
                if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR) {
                    $namespace .= $tokens[$i][1];
                }
                $i++;
            }
            $currentNamespace = $namespace;
            $ast['namespace'] = $currentNamespace;
        }
        
        // Class detection
        if ($id === T_CLASS) {
            $i = array_search($token, $tokens) + 1;
            
            // Skip if it's an anonymous class
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                $className = $tokens[$i][1];
                $class = ['name' => $className, 'methods' => []];
                
                // Check for extends
                while (isset($tokens[$i]) && $tokens[$i] !== '{') {
                    if (is_array($tokens[$i]) && $tokens[$i][0] === T_EXTENDS) {
                        $i++;
                        $extends = '';
                        while (isset($tokens[$i]) && is_array($tokens[$i]) && ($tokens[$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR)) {
                            $extends .= $tokens[$i][1];
                            $i++;
                        }
                        $class['extends'] = $extends;
                    }
                    
                    // Check for implements
                    if (is_array($tokens[$i]) && $tokens[$i][0] === T_IMPLEMENTS) {
                        $i++;
                        $implements = [];
                        $current = '';
                        
                        while (isset($tokens[$i]) && $tokens[$i] !== '{') {
                            if (is_array($tokens[$i])) {
                                if ($tokens[$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR) {
                                    $current .= $tokens[$i][1];
                                }
                            } else if ($tokens[$i] === ',') {
                                $implements[] = $current;
                                $current = '';
                            }
                            $i++;
                        }
                        
                        if ($current) {
                            $implements[] = $current;
                        }
                        
                        $class['implements'] = $implements;
                    }
                    
                    $i++;
                }
                
                $currentClass = $className;
                $classes[] = $class;
            }
        }
        
        // Method detection
        if ($id === T_FUNCTION && $currentClass !== null) {
            $i = array_search($token, $tokens) + 1;
            
            // Skip whitespace
            while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                $i++;
            }
            
            // Skip & for functions returning by reference
            if (isset($tokens[$i]) && $tokens[$i] === '&') {
                $i++;
                // Skip whitespace again
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++;
                }
            }
            
            if (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                $methodName = $tokens[$i][1];
                
                // Find the class in our list and add the method
                foreach ($classes as &$class) {
                    if ($class['name'] === $currentClass) {
                        $class['methods'][] = ['name' => $methodName];
                        break;
                    }
                }
            }
        }
        
        // Function detection (outside classes)
        if ($id === T_FUNCTION && $currentClass === null) {
            $i = array_search($token, $tokens) + 1;
            
            // Skip whitespace
            while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                $i++;
            }
            
            // Skip & for functions returning by reference
            if (isset($tokens[$i]) && $tokens[$i] === '&') {
                $i++;
                // Skip whitespace again
                while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                    $i++;
                }
            }
            
            if (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                $functionName = $tokens[$i][1];
                $functions[] = ['name' => $functionName];
            }
        }
        
        // Use statement detection
        if ($id === T_USE && $currentClass === null) {
            $i = array_search($token, $tokens) + 1;
            $import = '';
            $alias = null;
            
            while (isset($tokens[$i]) && $tokens[$i] !== ';') {
                if (is_array($tokens[$i])) {
                    if ($tokens[$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR) {
                        $import .= $tokens[$i][1];
                    } else if ($tokens[$i][0] === T_AS) {
                        // If there's an alias, next token is the alias name
                        $i++;
                        while (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_WHITESPACE) {
                            $i++;
                        }
                        if (isset($tokens[$i]) && is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                            $alias = $tokens[$i][1];
                        }
                    }
                }
                $i++;
            }
            
            if ($import) {
                $imports[] = ['path' => $import, 'alias' => $alias];
            }
        }
        
        // Include/require detection
        if (in_array($id, [T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE])) {
            $type = token_name($id);
            $i = array_search($token, $tokens) + 1;
            
            // Skip to the next string
            while (isset($tokens[$i])) {
                if (is_array($tokens[$i]) && in_array($tokens[$i][0], [T_CONSTANT_ENCAPSED_STRING, T_STRING])) {
                    $path = trim($tokens[$i][1], '\'"');
                    $includes[] = ['type' => $type, 'path' => $path];
                    break;
                }
                $i++;
            }
        }
        
        // End of class detection
        if ($id === T_CLOSE_TAG || $id === T_INLINE_HTML) {
            $currentClass = null;
        }
    } else {
        // Handle non-array tokens (single character tokens)
        if ($token === '}' && $currentClass !== null) {
            // Check if we're at the end of a class
            // This is a simplistic approach and might not work for nested structures
            $currentClass = null;
        }
    }
}

// Populate the AST
$ast['classes'] = $classes;
$ast['functions'] = $functions;
$ast['imports'] = $imports;
$ast['includes'] = $includes;

// Output as JSON
echo json_encode($ast);
'''
            
            temp_file.write(ast_script.encode('utf-8'))
        
        try:
            # Call PHP to execute the AST generation script
            result = subprocess.run(['php', temp_file_path], 
                                   capture_output=True, text=True, check=True)
            
            # Parse the JSON output
            ast_data = json.loads(result.stdout)
            return ast_data
            
        except subprocess.CalledProcessError as e:
            # Log error and fall back to regex-based parsing
            print(f"Error generating PHP AST: {e}", file=sys.stderr)
            print(f"STDERR: {e.stderr}", file=sys.stderr)
            return self._fallback_parsing(file_path)
            
        except json.JSONDecodeError as e:
            # Log error and fall back to regex-based parsing
            print(f"Error parsing PHP AST: {e}", file=sys.stderr)
            return self._fallback_parsing(file_path)
            
        finally:
            # Clean up temp file
            os.unlink(temp_file_path)
    
    def _fallback_parsing(self, file_path: str) -> Dict[str, Any]:
        """Fall back to regex-based parsing if AST parsing fails.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            Dictionary with parsed data
        """
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Initialize result
        result = {}
        
        # Find namespace
        namespace_match = re.search(r'namespace\s+([^;]+)', content)
        if namespace_match:
            result['namespace'] = namespace_match.group(1).strip()
        
        # Find classes
        classes = []
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?(?:\s+implements\s+([^{]+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            implements = match.group(3)
            
            class_info = {'name': class_name}
            
            if parent_class:
                class_info['extends'] = parent_class
                
            if implements:
                class_info['implements'] = [i.strip() for i in implements.split(',')]
            
            # Find methods
            method_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
            methods = []
            for method_match in method_matches:
                method_name = method_match.group(1)
                methods.append({'name': method_name})
            
            class_info['methods'] = methods
            classes.append(class_info)
        
        result['classes'] = classes
        
        # Find functions
        functions = []
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            functions.append({'name': function_name})
        
        result['functions'] = functions
        
        # Find imports
        imports = []
        import_matches = re.finditer(r'use\s+([^;]+)(?:\s+as\s+(\w+))?;', content)
        for match in import_matches:
            import_path = match.group(1).strip()
            alias = match.group(2)
            
            import_info = {'path': import_path}
            if alias:
                import_info['alias'] = alias
                
            imports.append(import_info)
        
        result['imports'] = imports
        
        # Find includes
        includes = []
        include_matches = re.finditer(r'(include|require|include_once|require_once)\s*\(?\s*[\'"]([^\'"]+)[\'"]', content)
        for match in include_matches:
            include_type = match.group(1)
            include_path = match.group(2)
            
            includes.append({'type': include_type, 'path': include_path})
        
        result['includes'] = includes
        
        return result

def main():
    """Test the PHP AST analyzer."""
    if len(sys.argv) < 2:
        print("Usage: php_ast_parser.py <php_file>")
        return 1
    
    file_path = sys.argv[1]
    if not os.path.exists(file_path) or not file_path.endswith('.php'):
        print(f"Error: {file_path} is not a valid PHP file")
        return 1
    
    # Import necessary components
    from php_analyzer_stub import MockSymbolRegistry
    
    # Initialize mock registry
    registry = MockSymbolRegistry()
    
    # Initialize analyzer
    analyzer = PHPAstAnalyzer(registry)
    
    # Analyze file
    try:
        result = analyzer.analyze_file(file_path)
        print(json.dumps(result, indent=2))
    except Exception as e:
        print(f"Error analyzing file: {e}")
        return 1
    
    return 0

if __name__ == '__main__':
    sys.exit(main())