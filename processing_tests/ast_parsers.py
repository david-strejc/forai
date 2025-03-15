#!/usr/bin/env python3
"""
Simplified AST Parsers for FORAI.

This script provides simplified implementations of PHP and JavaScript AST parsers
that don't rely on external processes or complex parsing.
"""

import os
import sys
import re
from typing import Dict, Any, List

class PHPAstAnalyzer:
    """Simplified AST analyzer for PHP files."""
    
    def __init__(self, symbol_registry):
        """Initialize the PHP AST analyzer."""
        self.registry = symbol_registry
        
    def analyze_file(self, file_path):
        """Analyze a PHP file using regex patterns.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Extract basic information using regex
        
        # Find namespace
        namespace = None
        namespace_match = re.search(r'namespace\s+([^;]+)', content)
        if namespace_match:
            namespace = namespace_match.group(1).strip()
        
        # Find class definitions
        classes = []
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, class_name, 'class')
            
            # Add class definition
            class_def = {
                'symbol_id': symbol_id,
                'name': class_name,
                'type': 'class'
            }
            
            # Add parent class if exists
            if parent_class:
                class_def['parents'] = [parent_class]
            
            classes.append(class_def)
            
            # Find class methods
            class_body_match = re.search(r'class\s+' + re.escape(class_name) + r'[^{]*{(.*?)}', content, re.DOTALL)
            if class_body_match:
                class_body = class_body_match.group(1)
                method_matches = re.finditer(r'function\s+(\w+)\s*\(', class_body)
                
                for method_match in method_matches:
                    method_name = method_match.group(1)
                    method_id = self.registry.get_symbol_id(file_id, method_name, 'function')
                    
                    method_def = {
                        'symbol_id': method_id,
                        'name': method_name,
                        'type': 'method',
                        'parent': symbol_id
                    }
                    
                    classes.append(method_def)
        
        # Find function definitions (outside classes)
        functions = []
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            
            # Skip if this is a class method (already handled)
            if any(def_item.get('name') == function_name and def_item.get('type') == 'method' 
                for def_item in classes):
                continue
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, function_name, 'function')
            
            # Add function definition
            functions.append({
                'symbol_id': symbol_id,
                'name': function_name,
                'type': 'function'
            })
        
        # Find imports (use statements)
        imports = []
        import_matches = re.finditer(r'use\s+([^;]+)(?:\s+as\s+(\w+))?', content)
        for match in import_matches:
            import_path = match.group(1).strip()
            
            # In a real implementation, we would resolve the import path
            # For now, just add it as unknown
            imports.append({
                'file_id': 'unknown',
                'symbol_id': '*'
            })
        
        # Combine definitions
        definitions = classes + functions
        
        # For PHP, all public classes and functions are exported
        exports = [def_item['symbol_id'] for def_item in definitions 
                  if not def_item['name'].startswith('_')]
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }

class JavaScriptAstAnalyzer:
    """Simplified AST analyzer for JavaScript files."""
    
    def __init__(self, symbol_registry):
        """Initialize the JavaScript AST analyzer."""
        self.registry = symbol_registry
        
    def analyze_file(self, file_path):
        """Analyze a JavaScript file using regex patterns.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Extract basic information using regex
        
        # Find class definitions
        classes = []
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, class_name, 'class')
            
            # Add class definition
            class_def = {
                'symbol_id': symbol_id,
                'name': class_name,
                'type': 'class'
            }
            
            # Add parent class if exists
            if parent_class:
                class_def['parents'] = [parent_class]
            
            classes.append(class_def)
            
            # Find class methods
            class_body_match = re.search(r'class\s+' + re.escape(class_name) + r'[^{]*{(.*?)}', content, re.DOTALL)
            if class_body_match:
                class_body = class_body_match.group(1)
                method_matches = re.finditer(r'(?:\w+\s+)?(\w+)\s*\([^)]*\)\s*{', class_body)
                
                for method_match in method_matches:
                    method_name = method_match.group(1)
                    
                    # Skip constructor
                    if method_name == 'constructor':
                        continue
                        
                    method_id = self.registry.get_symbol_id(file_id, method_name, 'function')
                    
                    method_def = {
                        'symbol_id': method_id,
                        'name': method_name,
                        'type': 'method',
                        'parent': symbol_id
                    }
                    
                    classes.append(method_def)
        
        # Find function definitions
        functions = []
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            
            # Skip if this is a class method (already handled)
            if any(def_item.get('name') == function_name and def_item.get('type') == 'method' 
                for def_item in classes):
                continue
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, function_name, 'function')
            
            # Add function definition
            functions.append({
                'symbol_id': symbol_id,
                'name': function_name,
                'type': 'function'
            })
        
        # Find variable function definitions
        variables = []
        var_function_matches = re.finditer(r'(?:const|let|var)\s+(\w+)\s*=\s*(?:function|\([^)]*\)\s*=>)', content)
        for match in var_function_matches:
            var_name = match.group(1)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, var_name, 'function')
            
            # Add function definition
            variables.append({
                'symbol_id': symbol_id,
                'name': var_name,
                'type': 'function'
            })
        
        # Find imports
        imports = []
        # import { name } from 'module'
        import_matches = re.finditer(r'import\s*{\s*([^}]+)\s*}\s*from\s*[\'"]([^\'"]+)[\'"]', content)
        for match in import_matches:
            import_names = [name.strip() for name in match.group(1).split(',')]
            module_name = match.group(2)
            
            for name in import_names:
                imports.append({
                    'file_id': 'unknown',
                    'symbol_id': name
                })
        
        # import name from 'module'
        import_default_matches = re.finditer(r'import\s+(\w+)\s+from\s*[\'"]([^\'"]+)[\'"]', content)
        for match in import_default_matches:
            import_name = match.group(1)
            module_name = match.group(2)
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': import_name
            })
        
        # Combine definitions
        definitions = classes + functions + variables
        
        # Find exports
        exports = []
        # export const/let/var/function/class name
        export_matches = re.finditer(r'export\s+(?:const|let|var|function|class)\s+(\w+)', content)
        for match in export_matches:
            export_name = match.group(1)
            
            # Find symbol ID for the exported symbol
            for def_item in definitions:
                if def_item['name'] == export_name:
                    exports.append(def_item['symbol_id'])
                    break
        
        # export { name }
        export_list_matches = re.finditer(r'export\s*{\s*([^}]+)\s*}', content)
        for match in export_list_matches:
            export_names = [name.strip().split(' as ')[0].strip() for name in match.group(1).split(',')]
            
            for name in export_names:
                for def_item in definitions:
                    if def_item['name'] == name:
                        exports.append(def_item['symbol_id'])
                        break
        
        # export default name
        export_default_matches = re.finditer(r'export\s+default\s+(\w+)', content)
        for match in export_default_matches:
            export_name = match.group(1)
            
            for def_item in definitions:
                if def_item['name'] == export_name:
                    exports.append(def_item['symbol_id'])
                    break
        
        # If no exports found, assume all definitions are exported
        if not exports:
            exports = [def_item['symbol_id'] for def_item in definitions]
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }