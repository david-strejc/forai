#!/usr/bin/env python3
"""
JavaScript AST Parser for FORAI.

This script implements an AST-based analyzer for JavaScript files.
It uses the esprima-python package to parse JavaScript code into an AST.
"""

import os
import sys
import json
import subprocess
import tempfile
import re
from typing import Dict, List, Optional, Any, Union

class JavaScriptAstAnalyzer:
    """AST-based analyzer for JavaScript files."""
    
    def __init__(self, symbol_registry):
        """Initialize the JavaScript AST analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a JavaScript file using AST.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Get AST using node.js subprocess
        ast_data = self._get_ast_for_file(file_path)
        
        # Parse AST to extract definitions, imports, exports
        definitions = []
        imports = []
        exports = []
        
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
                
                # Add parent class if extends
                if 'extends' in class_info and class_info['extends']:
                    class_def['parents'] = [class_info['extends']]
                
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
        
        # Extract variable definitions
        if 'variables' in ast_data:
            for var_info in ast_data['variables']:
                var_name = var_info['name']
                
                # Get symbol ID - treat as function if it's assigned a function
                symbol_type = 'function' if var_info.get('is_function', False) else 'variable'
                symbol_id = self.registry.get_symbol_id(file_id, var_name, symbol_type)
                
                # Create variable definition
                var_def = {
                    'symbol_id': symbol_id,
                    'name': var_name,
                    'type': symbol_type
                }
                
                definitions.append(var_def)
        
        # Extract imports
        if 'imports' in ast_data:
            for import_info in ast_data['imports']:
                # For now, set as unknown for file_id
                if 'specifiers' in import_info and import_info['specifiers']:
                    for specifier in import_info['specifiers']:
                        imports.append({
                            'file_id': 'unknown',
                            'symbol_id': specifier
                        })
                else:
                    imports.append({
                        'file_id': 'unknown',
                        'symbol_id': '*'
                    })
        
        # Extract exports
        if 'exports' in ast_data:
            for export_info in ast_data['exports']:
                export_name = export_info['name']
                
                # Find symbol ID for the exported symbol
                for def_item in definitions:
                    if def_item['name'] == export_name:
                        exports.append(def_item['symbol_id'])
                        break
        
        # If no exports found but export statements exist, include default export
        if not exports and ast_data.get('has_exports', False):
            # Use the first defined symbol as default export
            if definitions:
                exports.append(definitions[0]['symbol_id'])
        
        # If still no exports, assume all definitions are exported (common for older JS)
        if not exports:
            exports = [def_item['symbol_id'] for def_item in definitions]
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
    
    def _get_ast_for_file(self, file_path: str) -> Dict[str, Any]:
        """Generate and parse JavaScript AST for a file.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            Dictionary with AST data
        """
        # Read the content of the file
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Try to use Node.js for parsing
        try:
            # Create a temporary file for the Node.js AST generation script
            with tempfile.NamedTemporaryFile(suffix='.js', delete=False) as temp_file:
                temp_file_path = temp_file.name
                
                # Write Node.js script that generates AST as JSON
                ast_script = f'''
                const fs = require('fs');
                const path = require('path');
                
                // Create a simple AST parser
                function parseJavaScript(code) {{
                    const ast = {{
                        classes: [],
                        functions: [],
                        variables: [],
                        imports: [],
                        exports: [],
                        has_exports: false
                    }};
                    
                    try {{
                        // Simple class detection
                        const classRegex = /class\\s+(\\w+)(?:\\s+extends\\s+(\\w+))?\\s*{{/g;
                        let classMatch;
                        while ((classMatch = classRegex.exec(code)) !== null) {{
                            const className = classMatch[1];
                            const extendsClass = classMatch[2] || null;
                            
                            // Find class methods
                            const classBody = getClassBody(code, classMatch.index);
                            const methods = [];
                            
                            // Method pattern
                            const methodRegex = /(?:async\\s+)?(?:static\\s+)?([\\w$]+)\\s*\\([^)]*\\)\\s*{{/g;
                            let methodMatch;
                            while ((methodMatch = methodRegex.exec(classBody)) !== null) {{
                                // Skip constructor 
                                if (methodMatch[1] !== 'constructor') {{
                                    methods.push({{ name: methodMatch[1] }});
                                }}
                            }}
                            
                            const classInfo = {{ 
                                name: className,
                                methods: methods
                            }};
                            
                            if (extendsClass) {{
                                classInfo.extends = extendsClass;
                            }}
                            
                            ast.classes.push(classInfo);
                        }}
                        
                        // Function declarations
                        const functionRegex = /function\\s+(\\w+)\\s*\\([^)]*\\)/g;
                        let functionMatch;
                        while ((functionMatch = functionRegex.exec(code)) !== null) {{
                            ast.functions.push({{ name: functionMatch[1] }});
                        }}
                        
                        // Arrow function variable assignments
                        const arrowFunctionRegex = /(const|let|var)\\s+(\\w+)\\s*=\\s*(?:\\([^)]*\\)|\\w+)\\s*=>\\s*{{/g;
                        let arrowMatch;
                        while ((arrowMatch = arrowFunctionRegex.exec(code)) !== null) {{
                            ast.variables.push({{ 
                                name: arrowMatch[2],
                                is_function: true
                            }});
                        }}
                        
                        // Regular variable function assignments
                        const varFunctionRegex = /(const|let|var)\\s+(\\w+)\\s*=\\s*function\\s*\\(/g;
                        let varFunctionMatch;
                        while ((varFunctionMatch = varFunctionRegex.exec(code)) !== null) {{
                            ast.variables.push({{ 
                                name: varFunctionMatch[2],
                                is_function: true
                            }});
                        }}
                        
                        // Object properties that are functions
                        const objFunctionRegex = /(\\w+)\\s*:\\s*function\\s*\\(/g;
                        let objFunctionMatch;
                        while ((objFunctionMatch = objFunctionRegex.exec(code)) !== null) {{
                            ast.functions.push({{ name: objFunctionMatch[1] }});
                        }}
                        
                        // Regular variable declarations
                        const varRegex = /(const|let|var)\\s+(\\w+)\\s*=/g;
                        let varMatch;
                        while ((varMatch = varRegex.exec(code)) !== null) {{
                            // Check if we already captured this variable as a function
                            const varName = varMatch[2];
                            const alreadyDefined = ast.variables.some(v => v.name === varName);
                            
                            if (!alreadyDefined) {{
                                ast.variables.push({{ name: varName, is_function: false }});
                            }}
                        }}
                        
                        // ES6 imports
                        // import { symbol } from 'module'
                        const namedImportRegex = /import\\s*{{\\s*([^}}]+)\\s*}}\\s*from\\s*['"]([^'"]+)['"]/g;
                        let namedImportMatch;
                        while ((namedImportMatch = namedImportRegex.exec(code)) !== null) {{
                            const symbols = namedImportMatch[1].split(',').map(s => s.trim());
                            const moduleName = namedImportMatch[2];
                            
                            ast.imports.push({{
                                module: moduleName,
                                specifiers: symbols
                            }});
                        }}
                        
                        // import name from 'module'
                        const defaultImportRegex = /import\\s+(\\w+)\\s+from\\s*['"]([^'"]+)['"]/g;
                        let defaultImportMatch;
                        while ((defaultImportMatch = defaultImportRegex.exec(code)) !== null) {{
                            const importName = defaultImportMatch[1];
                            const moduleName = defaultImportMatch[2];
                            
                            ast.imports.push({{
                                module: moduleName,
                                specifiers: [importName]
                            }});
                        }}
                        
                        // import * as name from 'module'
                        const namespaceImportRegex = /import\\s*\\*\\s*as\\s*(\\w+)\\s*from\\s*['"]([^'"]+)['"]/g;
                        let namespaceImportMatch;
                        while ((namespaceImportMatch = namespaceImportRegex.exec(code)) !== null) {{
                            const namespaceName = namespaceImportMatch[1];
                            const moduleName = namespaceImportMatch[2];
                            
                            ast.imports.push({{
                                module: moduleName,
                                specifiers: [namespaceName]
                            }});
                        }}
                        
                        // CommonJS require
                        const requireRegex = /(const|let|var)\\s+(\\w+)\\s*=\\s*require\\s*\\(\\s*['"]([^'"]+)['"]/g;
                        let requireMatch;
                        while ((requireMatch = requireRegex.exec(code)) !== null) {{
                            const varName = requireMatch[2];
                            const moduleName = requireMatch[3];
                            
                            ast.imports.push({{
                                module: moduleName,
                                specifiers: [varName]
                            }});
                        }}
                        
                        // Named exports
                        // export const/let/var/function/class name
                        const namedExportRegex = /export\\s+(const|let|var|function|class)\\s+(\\w+)/g;
                        let namedExportMatch;
                        while ((namedExportMatch = namedExportRegex.exec(code)) !== null) {{
                            ast.exports.push({{ name: namedExportMatch[2] }});
                            ast.has_exports = true;
                        }}
                        
                        // export {{ name }}
                        const exportListRegex = /export\\s*{{\\s*([^}}]+)\\s*}}/g;
                        let exportListMatch;
                        while ((exportListMatch = exportListRegex.exec(code)) !== null) {{
                            const symbols = exportListMatch[1].split(',').map(s => s.trim());
                            
                            for (const symbol of symbols) {{
                                // Handle 'export { name as alias }'
                                const parts = symbol.split(/\\s+as\\s+/);
                                const exportName = parts[0].trim();
                                
                                ast.exports.push({{ name: exportName }});
                            }}
                            
                            ast.has_exports = true;
                        }}
                        
                        // export default name
                        const defaultExportRegex = /export\\s+default\\s+(\\w+)/g;
                        let defaultExportMatch;
                        while ((defaultExportMatch = defaultExportRegex.exec(code)) !== null) {{
                            ast.exports.push({{ name: defaultExportMatch[1] }});
                            ast.has_exports = true;
                        }}
                        
                    }} catch (e) {{
                        console.error('Error parsing JavaScript: ' + e.message);
                    }}
                    
                    return ast;
                }}
                
                // Helper function to extract class body
                function getClassBody(code, classStartIndex) {{
                    let openBraces = 0;
                    let startIndex = -1;
                    let endIndex = -1;
                    
                    for (let i = classStartIndex; i < code.length; i++) {{
                        if (code[i] === '{{') {{
                            if (openBraces === 0) {{
                                startIndex = i + 1;
                            }}
                            openBraces++;
                        }} else if (code[i] === '}}') {{
                            openBraces--;
                            if (openBraces === 0) {{
                                endIndex = i;
                                break;
                            }}
                        }}
                    }}
                    
                    if (startIndex !== -1 && endIndex !== -1) {{
                        return code.substring(startIndex, endIndex);
                    }}
                    
                    return '';
                }}
                
                // Read file content from the path directly
                const fs = require('fs');
                const fileContent = fs.readFileSync("''' + file_path + '''", 'utf8');
                
                // Parse and print AST
                const ast = parseJavaScript(fileContent);
                console.log(JSON.stringify(ast, null, 2));
                '''
                
                temp_file.write(ast_script.encode('utf-8'))
            
            # Execute the Node.js script
            result = subprocess.run(['node', temp_file_path], 
                                   capture_output=True, text=True, check=True)
            
            # Parse the JSON output
            ast_data = json.loads(result.stdout)
            return ast_data
            
        except Exception as e:
            # Log error and fall back to regex-based parsing
            print(f"Error generating JavaScript AST: {e}", file=sys.stderr)
            if hasattr(e, 'stderr'):
                print(f"STDERR: {e.stderr}", file=sys.stderr)
            return self._fallback_parsing(file_path)
            
        finally:
            # Clean up temp file
            if os.path.exists(temp_file_path):
                os.unlink(temp_file_path)
    
    def _fallback_parsing(self, file_path: str) -> Dict[str, Any]:
        """Fall back to regex-based parsing if AST parsing fails.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            Dictionary with parsed data
        """
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Initialize result
        result = {
            'classes': [],
            'functions': [],
            'variables': [],
            'imports': [],
            'exports': [],
            'has_exports': False
        }
        
        # Find classes
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            
            class_info = {'name': class_name, 'methods': []}
            
            if parent_class:
                class_info['extends'] = parent_class
                
            # Find class methods
            class_body_match = re.search(rf'class\s+{re.escape(class_name)}.*?{{(.*?)}}', content, re.DOTALL)
            if class_body_match:
                class_body = class_body_match.group(1)
                
                # Extract methods
                method_matches = re.finditer(r'(?:async\s+)?(?:static\s+)?(\w+)\s*\([^)]*\)\s*{', class_body)
                for method_match in method_matches:
                    method_name = method_match.group(1)
                    # Skip constructor
                    if method_name != 'constructor':
                        class_info['methods'].append({'name': method_name})
            
            result['classes'].append(class_info)
        
        # Find functions
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            result['functions'].append({'name': function_name})
        
        # Find arrow functions and function assignments
        arrow_func_matches = re.finditer(r'(const|let|var)\s+(\w+)\s*=\s*(?:\([^)]*\)|\w+)\s*=>\s*{', content)
        for match in arrow_func_matches:
            var_name = match.group(2)
            result['variables'].append({'name': var_name, 'is_function': True})
        
        # Find variable function assignments
        var_func_matches = re.finditer(r'(const|let|var)\s+(\w+)\s*=\s*function\s*\(', content)
        for match in var_func_matches:
            var_name = match.group(2)
            result['variables'].append({'name': var_name, 'is_function': True})
        
        # Find object function properties
        obj_func_matches = re.finditer(r'(\w+)\s*:\s*function\s*\(', content)
        for match in obj_func_matches:
            prop_name = match.group(1)
            result['functions'].append({'name': prop_name})
        
        # Find variable declarations
        var_matches = re.finditer(r'(const|let|var)\s+(\w+)\s*=', content)
        for match in var_matches:
            var_name = match.group(2)
            # Check if already defined as a function
            if not any(v['name'] == var_name for v in result['variables']):
                result['variables'].append({'name': var_name, 'is_function': False})
        
        # Find ES6 imports
        # import { symbol } from 'module'
        named_import_matches = re.finditer(r'import\s*{\s*([^}]+)\s*}\s*from\s*[\'"]([^\'"]+)[\'"]', content)
        for match in named_import_matches:
            symbols = [s.strip() for s in match.group(1).split(',')]
            module_name = match.group(2)
            
            result['imports'].append({
                'module': module_name,
                'specifiers': symbols
            })
        
        # import name from 'module'
        default_import_matches = re.finditer(r'import\s+(\w+)\s+from\s*[\'"]([^\'"]+)[\'"]', content)
        for match in default_import_matches:
            import_name = match.group(1)
            module_name = match.group(2)
            
            result['imports'].append({
                'module': module_name,
                'specifiers': [import_name]
            })
        
        # import * as name from 'module'
        namespace_import_matches = re.finditer(r'import\s*\*\s*as\s*(\w+)\s*from\s*[\'"]([^\'"]+)[\'"]', content)
        for match in namespace_import_matches:
            namespace_name = match.group(1)
            module_name = match.group(2)
            
            result['imports'].append({
                'module': module_name,
                'specifiers': [namespace_name]
            })
        
        # CommonJS require
        require_matches = re.finditer(r'(const|let|var)\s+(\w+)\s*=\s*require\s*\(\s*[\'"]([^\'"]+)[\'"]', content)
        for match in require_matches:
            var_name = match.group(2)
            module_name = match.group(3)
            
            result['imports'].append({
                'module': module_name,
                'specifiers': [var_name]
            })
        
        # Find exports
        # export const/let/var/function/class name
        named_export_matches = re.finditer(r'export\s+(const|let|var|function|class)\s+(\w+)', content)
        for match in named_export_matches:
            result['exports'].append({'name': match.group(2)})
            result['has_exports'] = True
        
        # export { name }
        export_list_matches = re.finditer(r'export\s*{\s*([^}]+)\s*}', content)
        for match in export_list_matches:
            symbols = [s.strip() for s in match.group(1).split(',')]
            
            for symbol in symbols:
                # Handle 'export { name as alias }'
                if ' as ' in symbol:
                    parts = symbol.split(' as ')
                    export_name = parts[0].strip()
                else:
                    export_name = symbol
                
                result['exports'].append({'name': export_name})
            
            result['has_exports'] = True
        
        # export default name
        default_export_matches = re.finditer(r'export\s+default\s+(\w+)', content)
        for match in default_export_matches:
            result['exports'].append({'name': match.group(1)})
            result['has_exports'] = True
        
        return result

def main():
    """Test the JavaScript AST analyzer."""
    if len(sys.argv) < 2:
        print("Usage: js_ast_parser.py <js_file>")
        return 1
    
    file_path = sys.argv[1]
    if not os.path.exists(file_path) or not file_path.endswith('.js'):
        print(f"Error: {file_path} is not a valid JavaScript file")
        return 1
    
    # Import necessary components
    from js_analyzer_stub import MockSymbolRegistry
    
    # Initialize mock registry
    registry = MockSymbolRegistry()
    
    # Initialize analyzer
    analyzer = JavaScriptAstAnalyzer(registry)
    
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