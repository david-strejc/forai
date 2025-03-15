#!/usr/bin/env python3
"""
FORAI Query API

A simple API for AI assistants to query FORAI headers.
"""

import argparse
import json
import os
import re
import sys
from typing import Dict, List, Optional, Any

class FORAIQueryEngine:
    """Query engine for FORAI headers."""
    
    def __init__(self, workspace_path: str):
        """Initialize the query engine.
        
        Args:
            workspace_path: Path to the workspace root
        """
        self.workspace_path = workspace_path
        self.registry_path = os.path.join(workspace_path, '.forai', 'registry.json')
        self.registry = self._load_registry()
        
    def _load_registry(self) -> Dict[str, Any]:
        """Load the symbol registry from disk."""
        if os.path.exists(self.registry_path):
            try:
                with open(self.registry_path, 'r') as f:
                    return json.load(f)
            except json.JSONDecodeError:
                return {
                    'files': {},
                    'file_paths': {}
                }
        return {
            'files': {},
            'file_paths': {}
        }
    
    def find_symbol_definition(self, symbol_name: str) -> Optional[Dict[str, str]]:
        """Find where a symbol is defined.
        
        Args:
            symbol_name: The name of the symbol to find
            
        Returns:
            A dictionary with file_id, file_path, and symbol_id, or None if not found
        """
        for file_id, file_info in self.registry.get('files', {}).items():
            for name, symbol_id in file_info.get('symbols', {}).items():
                if name == symbol_name:
                    file_path = os.path.join(self.workspace_path, file_info.get('path', ''))
                    return {
                        'file_id': file_id,
                        'file_path': file_path,
                        'symbol_id': symbol_id
                    }
        return None
    
    def get_file_symbols(self, file_path: str) -> Optional[Dict[str, Any]]:
        """Get all symbols defined in a file.
        
        Args:
            file_path: Path to the file
            
        Returns:
            A dictionary with file_id and symbols, or None if not found
        """
        rel_path = os.path.relpath(file_path, self.workspace_path)
        for file_id, file_info in self.registry.get('files', {}).items():
            if file_info.get('path') == rel_path:
                return {
                    'file_id': file_id,
                    'symbols': file_info.get('symbols', {})
                }
        return None
    
    def get_symbol_usages(self, symbol_name: str) -> List[Dict[str, str]]:
        """Find all files that use a symbol.
        
        Args:
            symbol_name: The name of the symbol to find
            
        Returns:
            A list of dictionaries with file_id and file_path
        """
        # First find the definition
        definition = self.find_symbol_definition(symbol_name)
        if not definition:
            return []
            
        # Then find all files that import it
        usages = []
        for file_id, file_info in self.registry.get('files', {}).items():
            file_path = os.path.join(self.workspace_path, file_info.get('path', ''))
            
            if not os.path.exists(file_path):
                continue
                
            with open(file_path, 'r') as f:
                content = f.read(2000)  # Read first 2000 characters to find header
            
            # Look for header
            header_match = re.search(r'//FORAI:(.*?)//', content)
            if not header_match:
                continue
                
            header = header_match.group(1)
            
            # Check if this file imports the symbol
            imp_pattern = f"{definition['file_id']}:{definition['symbol_id']}"
            star_pattern = f"{definition['file_id']}:\\*"
            
            if re.search(f"IMP\\[.*{imp_pattern}.*\\]", header) or re.search(f"IMP\\[.*{star_pattern}.*\\]", header):
                usages.append({
                    'file_id': file_id,
                    'file_path': file_path
                })
                
        return usages
        
    def list_all_symbols(self) -> Dict[str, Dict[str, str]]:
        """List all symbols in the workspace.
        
        Returns:
            A dictionary mapping symbol names to dictionaries with file_id and symbol_id
        """
        symbols = {}
        for file_id, file_info in self.registry.get('files', {}).items():
            for name, symbol_id in file_info.get('symbols', {}).items():
                symbols[name] = {
                    'file_id': file_id,
                    'symbol_id': symbol_id
                }
        return symbols
    
    def get_file_header(self, file_path: str) -> Optional[str]:
        """Get the FORAI header from a file.
        
        Args:
            file_path: Path to the file
            
        Returns:
            The FORAI header string, or None if not found
        """
        if not os.path.exists(file_path):
            return None
            
        with open(file_path, 'r') as f:
            content = f.read(2000)  # Read first 2000 characters to find header
        
        # Look for header
        header_match = re.search(r'//FORAI:(.*?)//', content)
        if not header_match:
            return None
            
        return f"//FORAI:{header_match.group(1)}//"


def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(description='FORAI Query API')
    parser.add_argument('--workspace', '-w', required=True, help='Path to workspace root')
    
    subparsers = parser.add_subparsers(dest='command', help='Command to run')
    
    # Find symbol definition
    find_parser = subparsers.add_parser('find', help='Find where a symbol is defined')
    find_parser.add_argument('symbol', help='Symbol name')
    
    # Get file symbols
    file_symbols_parser = subparsers.add_parser('file-symbols', help='Get all symbols defined in a file')
    file_symbols_parser.add_argument('file', help='Path to file')
    
    # Get symbol usages
    usages_parser = subparsers.add_parser('usages', help='Find all files that use a symbol')
    usages_parser.add_argument('symbol', help='Symbol name')
    
    # List all symbols
    list_parser = subparsers.add_parser('list', help='List all symbols in the workspace')
    
    # Get file header
    header_parser = subparsers.add_parser('header', help='Get the FORAI header from a file')
    header_parser.add_argument('file', help='Path to file')
    
    args = parser.parse_args()
    
    # Validate workspace path
    workspace_path = os.path.abspath(args.workspace)
    if not os.path.isdir(workspace_path):
        print(json.dumps({
            'success': False,
            'error': f"Workspace path does not exist: {workspace_path}"
        }))
        return 1
    
    # Initialize query engine
    query_engine = FORAIQueryEngine(workspace_path)
    
    try:
        if args.command == 'find':
            result = query_engine.find_symbol_definition(args.symbol)
            if result:
                print(json.dumps({
                    'success': True,
                    'result': result
                }))
            else:
                print(json.dumps({
                    'success': True,
                    'result': None,
                    'message': f"Symbol {args.symbol} not found"
                }))
        
        elif args.command == 'file-symbols':
            file_path = os.path.abspath(args.file)
            result = query_engine.get_file_symbols(file_path)
            if result:
                print(json.dumps({
                    'success': True,
                    'result': result
                }))
            else:
                print(json.dumps({
                    'success': True,
                    'result': None,
                    'message': f"File {file_path} not found in registry"
                }))
        
        elif args.command == 'usages':
            result = query_engine.get_symbol_usages(args.symbol)
            print(json.dumps({
                'success': True,
                'result': result
            }))
        
        elif args.command == 'list':
            result = query_engine.list_all_symbols()
            print(json.dumps({
                'success': True,
                'result': result
            }))
        
        elif args.command == 'header':
            file_path = os.path.abspath(args.file)
            result = query_engine.get_file_header(file_path)
            if result:
                print(json.dumps({
                    'success': True,
                    'result': result
                }))
            else:
                print(json.dumps({
                    'success': True,
                    'result': None,
                    'message': f"No FORAI header found in {file_path}"
                }))
        
        else:
            print(json.dumps({
                'success': False,
                'error': f"Unknown command: {args.command}"
            }))
            return 1
    
    except Exception as e:
        print(json.dumps({
            'success': False,
            'error': str(e)
        }))
        return 1
    
    return 0


if __name__ == '__main__':
    sys.exit(main())