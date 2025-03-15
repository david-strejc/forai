#!/usr/bin/env python3
"""
FORAI Dependency Resolver

This script updates FORAI headers to include proper dependencies between files.
"""

import os
import sys
import re
import json
import logging
from pathlib import Path

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class DependencyResolver:
    """Resolves dependencies between files and updates FORAI headers."""
    
    def __init__(self, base_dir):
        """Initialize the dependency resolver.
        
        Args:
            base_dir: Base directory containing the files
        """
        self.base_dir = os.path.abspath(base_dir)
        self.file_map = {}  # Maps file paths to file IDs
        self.exports_map = {}  # Maps (file_id, symbol_name) to symbol_id
        self.import_map = {}  # Maps import paths to file paths
        
    def scan_headers(self):
        """Scan all files for FORAI headers and build dependency maps."""
        for root, _, files in os.walk(self.base_dir):
            for file in files:
                if file.endswith(('.php', '.js')):
                    file_path = os.path.join(root, file)
                    self._process_header(file_path)
                    
    def _process_header(self, file_path):
        """Process a FORAI header in a file.
        
        Args:
            file_path: Path to the file
        """
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read(2000)  # Read first 2000 chars to find header
                
            header_match = re.search(r'//FORAI:(.*?)//', content)
            if not header_match:
                return
                
            header = header_match.group(0)
            
            # Extract file ID
            file_id_match = re.search(r'//FORAI:([^;]+);', header)
            if not file_id_match:
                return
                
            file_id = file_id_match.group(1)
            self.file_map[file_path] = file_id
            
            # Extract exports
            export_match = re.search(r'EXP\[(.*?)\]', header)
            if export_match and export_match.group(1):
                exports = export_match.group(1).split(',')
                
                # Extract definitions to map symbol_ids to names
                def_match = re.search(r'DEF\[(.*?)\]', header)
                if def_match and def_match.group(1):
                    def_parts = def_match.group(1).split(',')
                    for part in def_parts:
                        # Handle definitions with parents
                        if '<' in part:
                            symbol_info = part.split('<')[0]
                        else:
                            symbol_info = part
                            
                        if ':' in symbol_info:
                            symbol_id, symbol_name = symbol_info.split(':', 1)
                            
                            # Add to exports map if this symbol is exported
                            if symbol_id in exports:
                                rel_path = os.path.relpath(file_path, self.base_dir)
                                self.exports_map[(file_id, symbol_name)] = symbol_id
                                
                                # Add to import map for future resolution
                                if file_path.endswith('.js'):
                                    # For JS, use the module name convention
                                    module_name = os.path.splitext(rel_path)[0].replace('/', '.')
                                    self.import_map[module_name] = file_path
                                    
                                    # Also map views/x to views/x/index
                                    if '/views/' in module_name:
                                        base_name = module_name.split('.')[-1]
                                        parent_module = module_name.rsplit('.', 1)[0]
                                        self.import_map[f"{parent_module}.{base_name}.index"] = file_path
                                    
                                elif file_path.endswith('.php'):
                                    # For PHP, extract namespace + class
                                    namespace_match = re.search(r'namespace\s+([^;]+)', content)
                                    if namespace_match:
                                        namespace = namespace_match.group(1).strip()
                                        if symbol_name != namespace:
                                            full_path = f"{namespace}\\{symbol_name}"
                                            self.import_map[full_path] = file_path
            
        except Exception as e:
            logger.error(f"Error processing header in {file_path}: {e}")
            
    def update_imports(self):
        """Update import references in all FORAI headers."""
        updated_files = 0
        
        for file_path, file_id in self.file_map.items():
            try:
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    
                header_match = re.search(r'//FORAI:(.*?)//', content)
                if not header_match:
                    continue
                    
                header = header_match.group(0)
                
                # Extract current imports
                import_match = re.search(r'IMP\[(.*?)\]', header)
                if not import_match:
                    continue
                    
                # Parse the file to find imports
                if file_path.endswith('.js'):
                    imports = self._find_js_imports(file_path)
                elif file_path.endswith('.php'):
                    imports = self._find_php_imports(file_path)
                else:
                    continue
                
                # Format imports
                imp_parts = []
                for imp in imports:
                    imp_parts.append(f"{imp['file_id']}:{imp['symbol_id']}")
                    
                # Update header
                if imp_parts:
                    new_imp_section = f"IMP[{','.join(imp_parts)}]"
                else:
                    new_imp_section = "IMP[]"
                    
                current_imp_section = import_match.group(0)
                new_header = header.replace(current_imp_section, new_imp_section)
                
                if new_header != header:
                    updated_content = content.replace(header, new_header)
                    
                    with open(file_path, 'w', encoding='utf-8') as f:
                        f.write(updated_content)
                        
                    updated_files += 1
                    logger.info(f"Updated imports in {file_path}")
                    
            except Exception as e:
                logger.error(f"Error updating imports in {file_path}: {e}")
                
        return updated_files
        
    def _find_js_imports(self, file_path):
        """Find JavaScript imports in a file.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            List of imports with file_id and symbol_id
        """
        imports = []
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                
            # Find ES6 imports
            # import { symbol } from 'module'
            import_matches = re.finditer(r'import\s+\{([^}]+)\}\s+from\s+[\'"]([^\'"]+)[\'"]', content)
            for match in import_matches:
                imported_symbols = [s.strip() for s in match.group(1).split(',')]
                module_name = match.group(2)
                
                # Resolve module name to file path
                if module_name in self.import_map:
                    imported_file = self.import_map[module_name]
                    imported_file_id = self.file_map.get(imported_file)
                    
                    if imported_file_id:
                        for symbol in imported_symbols:
                            # Look up the symbol ID
                            if (imported_file_id, symbol) in self.exports_map:
                                symbol_id = self.exports_map[(imported_file_id, symbol)]
                                imports.append({
                                    'file_id': imported_file_id,
                                    'symbol_id': symbol_id
                                })
                            else:
                                # If we can't resolve the exact symbol, use *
                                imports.append({
                                    'file_id': imported_file_id,
                                    'symbol_id': '*'
                                })
            
            # import default from 'module'
            import_default_matches = re.finditer(r'import\s+(\w+)\s+from\s+[\'"]([^\'"]+)[\'"]', content)
            for match in import_default_matches:
                imported_symbol = match.group(1)
                module_name = match.group(2)
                
                # Resolve module name to file path
                if module_name in self.import_map:
                    imported_file = self.import_map[module_name]
                    imported_file_id = self.file_map.get(imported_file)
                    
                    if imported_file_id:
                        # For default imports, assume the first export is the default
                        imports.append({
                            'file_id': imported_file_id,
                            'symbol_id': '*'
                        })
                
        except Exception as e:
            logger.error(f"Error finding JS imports in {file_path}: {e}")
            
        return imports
        
    def _find_php_imports(self, file_path):
        """Find PHP imports in a file.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            List of imports with file_id and symbol_id
        """
        imports = []
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                
            # Find 'use' statements
            use_matches = re.finditer(r'use\s+([^;]+);', content)
            for match in use_matches:
                use_path = match.group(1).strip()
                
                # Handle aliased imports: use Some\Namespace\Class as Alias
                if ' as ' in use_path:
                    parts = use_path.split(' as ')
                    use_path = parts[0].strip()
                
                # Resolve class name to file path
                if use_path in self.import_map:
                    imported_file = self.import_map[use_path]
                    imported_file_id = self.file_map.get(imported_file)
                    
                    if imported_file_id:
                        # Extract the class name from the use path
                        class_name = use_path.split('\\')[-1]
                        
                        # Look up the symbol ID
                        if (imported_file_id, class_name) in self.exports_map:
                            symbol_id = self.exports_map[(imported_file_id, class_name)]
                            imports.append({
                                'file_id': imported_file_id,
                                'symbol_id': symbol_id
                            })
                        else:
                            # If we can't resolve the exact symbol, use *
                            imports.append({
                                'file_id': imported_file_id,
                                'symbol_id': '*'
                            })
                
        except Exception as e:
            logger.error(f"Error finding PHP imports in {file_path}: {e}")
            
        return imports

def main():
    """Run the dependency resolver on a directory."""
    if len(sys.argv) < 2:
        print("Usage: dependency_resolver.py <directory>")
        return 1
        
    directory = sys.argv[1]
    if not os.path.isdir(directory):
        print(f"Error: {directory} is not a directory")
        return 1
        
    # Initialize and run dependency resolver
    resolver = DependencyResolver(directory)
    
    # First scan to build dependency maps
    logger.info(f"Scanning headers in {directory}")
    resolver.scan_headers()
    logger.info(f"Found {len(resolver.file_map)} files with FORAI headers")
    logger.info(f"Found {len(resolver.exports_map)} exported symbols")
    logger.info(f"Built {len(resolver.import_map)} import path mappings")
    
    # Update imports
    logger.info("Updating import references")
    updated_files = resolver.update_imports()
    logger.info(f"Updated {updated_files} files with resolved import references")
    
    return 0

if __name__ == '__main__':
    sys.exit(main())