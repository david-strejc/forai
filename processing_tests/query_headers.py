#!/usr/bin/env python3
"""
FORAI Query Headers

This script queries FORAI headers from files and extracts information.
"""

import os
import sys
import re
import json
import logging
from pathlib import Path
import argparse

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def extract_file_id(header):
    """Extract file ID from a FORAI header."""
    match = re.search(r'//FORAI:([^;]+);', header)
    if match:
        return match.group(1)
    return None

def extract_definitions(header):
    """Extract definitions from a FORAI header."""
    match = re.search(r'DEF\[(.*?)\]', header)
    if not match:
        return []
        
    content = match.group(1)
    if not content:
        return []
        
    # Split by comma but only if the comma is not inside a <>
    in_angle = False
    current = ""
    parts = []
    for char in content:
        if char == '<':
            in_angle = True
            current += char
        elif char == '>':
            in_angle = False
            current += char
        elif char == ',' and not in_angle:
            parts.append(current)
            current = ""
        else:
            current += char
    
    if current:
        parts.append(current)
    
    # Parse each definition
    definitions = []
    for part in parts:
        # For complex definitions with parents
        parent_match = re.match(r'([^:]+):([^<]+)<(.+)>', part)
        if parent_match:
            symbol_id, name, parents = parent_match.groups()
            definitions.append({
                'symbol_id': symbol_id,
                'name': name,
                'parents': parents.split(',')
            })
        else:
            # For simple definitions
            simple_match = re.match(r'([^:]+):(.*)', part)
            if simple_match:
                symbol_id, name = simple_match.groups()
                definitions.append({
                    'symbol_id': symbol_id,
                    'name': name
                })
    
    return definitions

def extract_imports(header):
    """Extract imports from a FORAI header."""
    match = re.search(r'IMP\[(.*?)\]', header)
    if not match:
        return []
        
    content = match.group(1)
    if not content:
        return []
        
    imports = []
    for part in content.split(','):
        # Parse file_id:symbol_id
        import_match = re.match(r'([^:]+):(.*)', part)
        if import_match:
            file_id, symbol_id = import_match.groups()
            imports.append({
                'file_id': file_id,
                'symbol_id': symbol_id
            })
    
    return imports

def extract_exports(header):
    """Extract exports from a FORAI header."""
    match = re.search(r'EXP\[(.*?)\]', header)
    if not match:
        return []
        
    content = match.group(1)
    if not content:
        return []
        
    return content.split(',')

def extract_language(header):
    """Extract language from a FORAI header."""
    match = re.search(r'LANG\[(.*?)\]', header)
    if match:
        return match.group(1)
    return None

def get_header_from_file(file_path):
    """Extract FORAI header from a file."""
    try:
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(2000)  # Read first 2000 characters
        
        # Look for header
        header_match = re.search(r'//FORAI:(.*?)//', content)
        if not header_match:
            return None
            
        return header_match.group(0)
    except Exception as e:
        logger.error(f"Error reading file {file_path}: {e}")
        return None

def parse_header(header):
    """Parse a FORAI header and extract all components."""
    if not header:
        return None
        
    return {
        'file_id': extract_file_id(header),
        'definitions': extract_definitions(header),
        'imports': extract_imports(header),
        'exports': extract_exports(header),
        'language': extract_language(header)
    }

def query_headers(directory):
    """
    Query all FORAI headers in a directory.
    
    Args:
        directory: Directory containing the files
        
    Returns:
        Dictionary with statistics and headers
    """
    headers = {}
    stats = {
        'total_files': 0,
        'files_with_headers': 0,
        'php_files': 0,
        'js_files': 0,
        'php_with_headers': 0,
        'js_with_headers': 0,
        'total_definitions': 0,
        'total_imports': 0,
        'total_exports': 0
    }
    
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            file_ext = os.path.splitext(file)[1].lower()
            
            if file_ext in ['.php', '.js']:
                stats['total_files'] += 1
                
                if file_ext == '.php':
                    stats['php_files'] += 1
                elif file_ext == '.js':
                    stats['js_files'] += 1
                
                # Get header
                header = get_header_from_file(file_path)
                if header:
                    stats['files_with_headers'] += 1
                    
                    if file_ext == '.php':
                        stats['php_with_headers'] += 1
                    elif file_ext == '.js':
                        stats['js_with_headers'] += 1
                    
                    # Parse header
                    header_info = parse_header(header)
                    if header_info:
                        headers[file_path] = header_info
                        
                        # Update stats
                        stats['total_definitions'] += len(header_info.get('definitions', []))
                        stats['total_imports'] += len(header_info.get('imports', []))
                        stats['total_exports'] += len(header_info.get('exports', []))
    
    return {
        'stats': stats,
        'headers': headers
    }

def show_file_header(file_path):
    """
    Show the FORAI header for a specific file.
    
    Args:
        file_path: Path to the file
        
    Returns:
        Parsed header information
    """
    header = get_header_from_file(file_path)
    if not header:
        logger.error(f"No FORAI header found in {file_path}")
        return None
        
    return parse_header(header)

def main():
    """Query FORAI headers."""
    parser = argparse.ArgumentParser(description='Query FORAI headers from files')
    subparsers = parser.add_subparsers(dest='command', help='Commands')
    
    # Directory command
    dir_parser = subparsers.add_parser('directory', help='Query headers in a directory')
    dir_parser.add_argument('directory', help='Directory to search in')
    dir_parser.add_argument('--output', help='Output file for detailed results (JSON)')
    
    # File command
    file_parser = subparsers.add_parser('file', help='Query header in a specific file')
    file_parser.add_argument('file', help='File to query')
    
    args = parser.parse_args()
    
    if args.command == 'directory':
        if not os.path.isdir(args.directory):
            logger.error(f"Directory not found: {args.directory}")
            return 1
            
        logger.info(f"Querying headers in {args.directory}")
        result = query_headers(args.directory)
        
        # Print summary
        stats = result['stats']
        logger.info("\n===== Header Statistics =====")
        logger.info(f"Total files: {stats['total_files']}")
        logger.info(f"  PHP files: {stats['php_files']}")
        logger.info(f"  JS files: {stats['js_files']}")
        logger.info(f"Files with headers: {stats['files_with_headers']} "
                   f"({stats['files_with_headers']/stats['total_files']*100:.1f}%)")
        logger.info(f"  PHP files: {stats['php_with_headers']} "
                   f"({stats['php_with_headers']/stats['php_files']*100:.1f}%)")
        logger.info(f"  JS files: {stats['js_with_headers']} "
                   f"({stats['js_with_headers']/stats['js_files']*100:.1f}%)")
        logger.info(f"Total definitions: {stats['total_definitions']}")
        logger.info(f"Total imports: {stats['total_imports']}")
        logger.info(f"Total exports: {stats['total_exports']}")
        
        # Save detailed results to file
        if args.output:
            with open(args.output, 'w', encoding='utf-8') as f:
                json.dump(result, f, indent=2)
            logger.info(f"Detailed results saved to {args.output}")
            
    elif args.command == 'file':
        if not os.path.isfile(args.file):
            logger.error(f"File not found: {args.file}")
            return 1
            
        logger.info(f"Querying header in {args.file}")
        header_info = show_file_header(args.file)
        
        if header_info:
            logger.info("\n===== Header Information =====")
            logger.info(f"File ID: {header_info['file_id']}")
            logger.info(f"Language: {header_info['language']}")
            
            logger.info("\nDefinitions:")
            for definition in header_info['definitions']:
                parents = definition.get('parents', [])
                if parents:
                    logger.info(f"  {definition['symbol_id']}: {definition['name']} (extends {', '.join(parents)})")
                else:
                    logger.info(f"  {definition['symbol_id']}: {definition['name']}")
            
            logger.info("\nImports:")
            for imp in header_info['imports']:
                logger.info(f"  {imp['file_id']}:{imp['symbol_id']}")
            
            logger.info("\nExports:")
            for exp in header_info['exports']:
                logger.info(f"  {exp}")
    else:
        parser.print_help()
    
    return 0

if __name__ == '__main__':
    sys.exit(main())