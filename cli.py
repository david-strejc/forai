#!/usr/bin/env python3
"""
FORAI Command Line Interface

A tool for managing FORAI headers in Python files.
"""

import argparse
import logging
import os
import sys
import json
from typing import Dict, Any

from forai.symbol_registry import SymbolRegistry
from forai.static_analyzer import StaticAnalyzer
from forai.runtime_introspector import RuntimeIntrospector
from forai.header_generator import HeaderGenerator
from forai.dependency_tracker import DependencyTracker

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(levelname)s: %(message)s')
logger = logging.getLogger(__name__)

def merge_static_and_runtime(static_data: Dict[str, Any], runtime_data: Dict[str, Any]) -> Dict[str, Any]:
    """Merge static and runtime analysis results.
    
    Args:
        static_data: The static analysis data
        runtime_data: The runtime introspection data
        
    Returns:
        The merged data
    """
    # Start with the static data
    merged = dict(static_data)
    
    # Get runtime symbols
    runtime_symbols = runtime_data.get('runtime_symbols', {})
    
    # Update definitions with runtime information
    if 'definitions' in merged:
        for i, defn in enumerate(merged['definitions']):
            name = defn.get('name')
            if name in runtime_symbols:
                # Add or update information from runtime analysis
                runtime_info = runtime_symbols[name]
                
                # For classes, merge parent information
                if defn.get('type') == 'class' and 'bases' in runtime_info:
                    # For now, just log the additional information
                    # In a real implementation, you would merge the parent information
                    logger.debug(f"Runtime found additional parents for {name}: {runtime_info['bases']}")
    
    return merged

def update_file_header(file_path: str, registry: SymbolRegistry, enable_runtime: bool) -> bool:
    """Update the FORAI header in a file.
    
    Args:
        file_path: Path to the file
        registry: The symbol registry
        enable_runtime: Whether to use runtime introspection
        
    Returns:
        True if the imports changed, False otherwise
    """
    # Initialize components
    analyzer = StaticAnalyzer(registry)
    header_generator = HeaderGenerator(registry)
    
    # Get previous header imports
    previous_imports = set()
    try:
        dependency_tracker = DependencyTracker(registry)
        previous_imports = set(dependency_tracker._get_header_imports(file_path))
    except Exception as e:
        logger.debug(f"Failed to get previous imports: {e}")
    
    # Analyze file
    file_data = analyzer.analyze_file(file_path)
    
    # Add runtime information if requested
    if enable_runtime:
        runtime_introspector = RuntimeIntrospector()
        runtime_data = runtime_introspector.introspect(file_path)
        file_data = merge_static_and_runtime(file_data, runtime_data)
    
    # Generate header
    header = header_generator.generate_header(file_data)
    
    # Update file
    header_generator.update_file_header(file_path, header)
    
    # Check if imports changed
    current_imports = set()
    for imp in file_data.get('imports', []):
        file_id = imp.get('file_id', '')
        symbol_id = imp.get('symbol_id', '*')
        if file_id:
            current_imports.add(f"{file_id}:{symbol_id}")
    
    return previous_imports != current_imports

def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(description='FORAI Header Tool')
    parser.add_argument('--workspace', '-w', required=True, help='Path to workspace root')
    parser.add_argument('--runtime', '-r', action='store_true', help='Enable runtime introspection')
    
    subparsers = parser.add_subparsers(dest='command', help='Command to run')
    
    # Update a single file
    update_parser = subparsers.add_parser('update', help='Update a single file')
    update_parser.add_argument('file', help='Path to file')
    
    # Update all Python files
    update_all_parser = subparsers.add_parser('update-all', help='Update all Python files')
    
    # Rename a file
    rename_parser = subparsers.add_parser('rename', help='Handle file rename')
    rename_parser.add_argument('old_path', help='Old file path')
    rename_parser.add_argument('new_path', help='New file path')
    
    # Update dependencies
    deps_parser = subparsers.add_parser('update-deps', help='Update dependent files')
    deps_parser.add_argument('file', help='Path to the changed file')
    
    # List dependencies
    list_deps_parser = subparsers.add_parser('list-deps', help='List dependencies')
    list_deps_parser.add_argument('file', help='Path to file')
    
    args = parser.parse_args()
    
    # Validate workspace path
    workspace_path = os.path.abspath(args.workspace)
    if not os.path.isdir(workspace_path):
        logger.error(f"Workspace path does not exist: {workspace_path}")
        return 1
    
    # Initialize registry
    registry = SymbolRegistry(workspace_path)
    
    try:
        if args.command == 'update':
            # Validate file path
            file_path = os.path.abspath(args.file)
            if not os.path.isfile(file_path):
                logger.error(f"File does not exist: {file_path}")
                return 1
                
            # Update file header
            imports_changed = update_file_header(file_path, registry, args.runtime)
            
            # If imports changed, update dependent files
            if imports_changed:
                file_id = registry.get_file_id(file_path)
                analyzer = StaticAnalyzer(registry)
                header_generator = HeaderGenerator(registry)
                dependency_tracker = DependencyTracker(registry)
                dependency_tracker.update_dependent_headers(file_id, analyzer, header_generator, args.runtime)
                
            logger.info(f"Updated FORAI header for {file_path}")
            
            # Return success with JSON
            print(json.dumps({
                'success': True,
                'imports_changed': imports_changed
            }))
            
        elif args.command == 'update-all':
            # Find all Python files
            python_files = []
            for root, _, files in os.walk(workspace_path):
                for file in files:
                    if file.endswith('.py'):
                        python_files.append(os.path.join(root, file))
            
            # Update each file
            updated = 0
            for file_path in python_files:
                try:
                    update_file_header(file_path, registry, args.runtime)
                    updated += 1
                except Exception as e:
                    logger.error(f"Failed to update {file_path}: {e}")
            
            logger.info(f"Updated FORAI headers for {updated} files")
            
            # Return success with JSON
            print(json.dumps({
                'success': True,
                'updated': updated,
                'total': len(python_files)
            }))
            
        elif args.command == 'rename':
            # Validate file paths
            old_path = os.path.abspath(args.old_path)
            new_path = os.path.abspath(args.new_path)
            
            if not os.path.isfile(new_path):
                logger.error(f"New file does not exist: {new_path}")
                return 1
                
            # Update registry
            file_id = registry.update_file_path(old_path, new_path)
            
            # Update header in the new file
            imports_changed = update_file_header(new_path, registry, args.runtime)
            
            # Update dependent files
            analyzer = StaticAnalyzer(registry)
            header_generator = HeaderGenerator(registry)
            dependency_tracker = DependencyTracker(registry)
            dependency_tracker.update_dependent_headers(file_id, analyzer, header_generator, args.runtime)
            
            logger.info(f"Updated FORAI header for renamed file {new_path}")
            
            # Return success with JSON
            print(json.dumps({
                'success': True,
                'imports_changed': imports_changed
            }))
            
        elif args.command == 'update-deps':
            # Validate file path
            file_path = os.path.abspath(args.file)
            if not os.path.isfile(file_path):
                logger.error(f"File does not exist: {file_path}")
                return 1
                
            # Update dependent files
            file_id = registry.get_file_id(file_path)
            analyzer = StaticAnalyzer(registry)
            header_generator = HeaderGenerator(registry)
            dependency_tracker = DependencyTracker(registry)
            dependency_tracker.update_dependent_headers(file_id, analyzer, header_generator, args.runtime)
            
            logger.info(f"Updated FORAI headers for files depending on {file_path}")
            
            # Return success with JSON
            print(json.dumps({
                'success': True
            }))
            
        elif args.command == 'list-deps':
            # Validate file path
            file_path = os.path.abspath(args.file)
            if not os.path.isfile(file_path):
                logger.error(f"File does not exist: {file_path}")
                return 1
                
            # Get file ID
            file_id = registry.get_file_id(file_path)
            
            # Get affected files
            dependency_tracker = DependencyTracker(registry)
            affected_files = dependency_tracker.get_affected_files(file_id)
            
            # Convert file IDs to paths
            affected_paths = []
            for affected_id in affected_files:
                file_info = registry.registry['files'].get(affected_id)
                if file_info:
                    affected_paths.append(os.path.join(workspace_path, file_info['path']))
            
            # Return results
            print(json.dumps({
                'success': True,
                'file_id': file_id,
                'dependencies': affected_paths
            }))
            
        else:
            logger.error(f"Unknown command: {args.command}")
            parser.print_help()
            return 1
            
    except Exception as e:
        logger.error(f"Error: {e}")
        print(json.dumps({
            'success': False,
            'error': str(e)
        }))
        return 1
        
    return 0

if __name__ == '__main__':
    sys.exit(main())