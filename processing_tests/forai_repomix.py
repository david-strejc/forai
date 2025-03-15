#!/usr/bin/env python3
"""
FORAI RepomiX

This script extracts FORAI headers from files and creates a comprehensive representation
of the codebase, similar to RepomiX. It counts tokens and creates an AI-friendly format
of the codebase.
"""

import os
import sys
import re
import json
import logging
import argparse
from typing import Dict, List, Any, Optional, Set, Tuple
from pathlib import Path

# Try to import tiktoken, but provide a fallback if not available
try:
    import tiktoken
except ImportError:
    logging.warning("tiktoken not found, using a simple word-based token counter")
    # Provide a simple fallback for token counting
    class SimpleTiktoken:
        def __init__(self, *args, **kwargs):
            pass

        def encode(self, text):
            # Simple approx: split by whitespace and punctuation, count words
            # This is obviously not accurate but serves as a fallback
            import re
            tokens = re.findall(r'\b\w+\b', text)
            return tokens

        def decode(self, tokens):
            # Join tokens with spaces (not accurate but simple fallback)
            if isinstance(tokens, list):
                return ' '.join(tokens)
            return str(tokens)

        def get_encoding(self, _):
            return self

    # Create a mock tiktoken module
    tiktoken = SimpleTiktoken()
    tiktoken.get_encoding = lambda _: SimpleTiktoken()

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class ForaiHeaderReader:
    """Read and parse FORAI headers from files."""
    
    def __init__(self):
        """Initialize the header reader."""
        self.header_pattern = re.compile(r'//FORAI:(.*?)//')
        
    def extract_header(self, file_path: str) -> Optional[Dict[str, Any]]:
        """Extract FORAI header from a file.
        
        Args:
            file_path: Path to the file
            
        Returns:
            Dictionary with header data, or None if no header found
        """
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read(2000)  # Read first 2000 chars to find header
                
            header_match = self.header_pattern.search(content)
            if not header_match:
                return None
                
            header_text = header_match.group(1)
            
            # Parse header components
            components = header_text.split(';')
            
            # Extract file ID
            file_id = components[0].strip()
            
            # Extract definitions
            definitions = []
            for component in components:
                if component.startswith('DEF['):
                    def_text = component[4:-1]  # Remove DEF[ and ]
                    if def_text:
                        def_parts = def_text.split(',')
                        for part in def_parts:
                            # Handle parent classes
                            if '<' in part:
                                symbol_info, parent_info = part.split('<', 1)
                                if ':' in symbol_info:
                                    symbol_id, symbol_name = symbol_info.split(':', 1)
                                    definitions.append({
                                        'id': symbol_id,
                                        'name': symbol_name,
                                        'parent': parent_info
                                    })
                            elif ':' in part:
                                symbol_id, symbol_name = part.split(':', 1)
                                definitions.append({
                                    'id': symbol_id,
                                    'name': symbol_name
                                })
            
            # Extract imports
            imports = []
            for component in components:
                if component.startswith('IMP['):
                    imp_text = component[4:-1]  # Remove IMP[ and ]
                    if imp_text:
                        imp_parts = imp_text.split(',')
                        for part in imp_parts:
                            if ':' in part:
                                file_ref, symbol_ref = part.split(':', 1)
                                imports.append({
                                    'file_id': file_ref,
                                    'symbol_id': symbol_ref
                                })
            
            # Extract exports
            exports = []
            for component in components:
                if component.startswith('EXP['):
                    exp_text = component[4:-1]  # Remove EXP[ and ]
                    if exp_text:
                        exports = exp_text.split(',')
            
            # Extract language
            language = 'unknown'
            for component in components:
                if component.startswith('LANG['):
                    language = component[5:-1]  # Remove LANG[ and ]
            
            return {
                'file_id': file_id,
                'file_path': file_path,
                'definitions': definitions,
                'imports': imports,
                'exports': exports,
                'language': language
            }
        
        except Exception as e:
            logger.error(f"Error extracting header from {file_path}: {e}")
            return None

class ForaiRepomiX:
    """Generate a RepomiX-like file from FORAI headers."""
    
    def __init__(self, encoding_name: str = "cl100k_base"):
        """Initialize the RepomiX generator.
        
        Args:
            encoding_name: The name of the tokenizer encoding to use
        """
        self.header_reader = ForaiHeaderReader()
        self.file_data = {}
        self.file_ids = {}
        self.encoding = tiktoken.get_encoding(encoding_name)
        
    def scan_directory(self, directory: str) -> Dict[str, Any]:
        """Scan a directory for files with FORAI headers.
        
        Args:
            directory: Directory to scan
            
        Returns:
            Dictionary with summary statistics
        """
        file_count = 0
        header_count = 0
        languages = {}
        
        for root, _, files in os.walk(directory):
            for file in files:
                file_path = os.path.join(root, file)
                file_count += 1
                
                # Extract header
                header_data = self.header_reader.extract_header(file_path)
                if header_data:
                    self.file_data[file_path] = header_data
                    self.file_ids[header_data['file_id']] = file_path
                    header_count += 1
                    
                    # Track languages
                    language = header_data['language']
                    languages[language] = languages.get(language, 0) + 1
        
        return {
            'total_files': file_count,
            'files_with_headers': header_count,
            'languages': languages
        }
    
    def get_file_content(self, file_path: str, max_token_count: int = 1000) -> str:
        """Get file content with limited token count.
        
        Args:
            file_path: Path to the file
            max_token_count: Maximum number of tokens to extract
            
        Returns:
            Truncated file content
        """
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
            
            # Count tokens
            tokens = self.encoding.encode(content)
            if len(tokens) > max_token_count:
                # Truncate to max tokens
                tokens = tokens[:max_token_count]
                content = self.encoding.decode(tokens)
                content += "... [content truncated to save tokens]"
            
            return content
        except Exception as e:
            logger.error(f"Error reading content from {file_path}: {e}")
            return "[Error reading file content]"
    
    def count_tokens(self, text: str) -> int:
        """Count tokens in a text.
        
        Args:
            text: Text to count tokens for
            
        Returns:
            Number of tokens
        """
        tokens = self.encoding.encode(text)
        return len(tokens)
    
    def resolve_dependencies(self) -> Dict[str, List[str]]:
        """Resolve file dependencies based on imports.
        
        Returns:
            Dictionary with file dependencies
        """
        dependencies = {}
        
        for file_path, header_data in self.file_data.items():
            deps = []
            
            # Get imports
            for imp in header_data['imports']:
                file_id = imp['file_id']
                
                # Skip unknown files
                if file_id == 'unknown':
                    continue
                
                # Resolve file path
                if file_id in self.file_ids:
                    dep_file = self.file_ids[file_id]
                    if dep_file != file_path:  # Skip self-dependencies
                        deps.append(dep_file)
            
            dependencies[file_path] = deps
        
        return dependencies
    
    def generate_repomix(self, output_file: str, include_content: bool = True, 
                        max_token_count: int = 1000) -> Dict[str, Any]:
        """Generate a RepomiX-like file.
        
        Args:
            output_file: Path to the output file
            include_content: Whether to include file content (default: True)
            max_token_count: Maximum token count per file (default: 1000)
            
        Returns:
            Dictionary with statistics
        """
        # Prepare output
        output_lines = []
        output_lines.append("# FORAI RepomiX")
        output_lines.append("A machine-readable representation of the codebase with FORAI headers.")
        output_lines.append("")
        
        # Add table of contents
        output_lines.append("## Table of Contents")
        for language, count in sorted(self.language_stats().items()):
            output_lines.append(f"- {language}: {count} files")
        output_lines.append("")
        
        # Add file metrics
        dependencies = self.resolve_dependencies()
        
        # Sort files by language and path
        sorted_files = sorted(
            self.file_data.items(), 
            key=lambda x: (x[1]['language'], x[0])
        )
        
        # Add file entries
        current_language = None
        file_token_counts = {}
        for file_path, header_data in sorted_files:
            language = header_data['language']
            
            # Add language section
            if language != current_language:
                output_lines.append(f"## {language.upper()} Files")
                output_lines.append("")
                current_language = language
            
            # Relative path for display
            rel_path = os.path.relpath(file_path)
            
            # Add file header
            output_lines.append(f"### {rel_path}")
            output_lines.append(f"**File ID:** `{header_data['file_id']}`")
            
            # Add definitions
            if header_data['definitions']:
                output_lines.append("**Definitions:**")
                for defn in header_data['definitions']:
                    if 'parent' in defn:
                        output_lines.append(f"- `{defn['id']}`: {defn['name']} (extends {defn['parent']})")
                    else:
                        output_lines.append(f"- `{defn['id']}`: {defn['name']}")
            
            # Add exports
            if header_data['exports']:
                output_lines.append("**Exports:**")
                export_lines = []
                for exp_id in header_data['exports']:
                    # Try to find definition name
                    name = None
                    for defn in header_data['definitions']:
                        if defn['id'] == exp_id:
                            name = defn['name']
                            break
                    
                    if name:
                        export_lines.append(f"- `{exp_id}`: {name}")
                    else:
                        export_lines.append(f"- `{exp_id}`")
                        
                output_lines.extend(export_lines)
            
            # Add dependencies
            if file_path in dependencies and dependencies[file_path]:
                output_lines.append("**Dependencies:**")
                for dep in dependencies[file_path]:
                    dep_rel_path = os.path.relpath(dep)
                    dep_id = self.file_data[dep]['file_id']
                    output_lines.append(f"- `{dep_id}`: {dep_rel_path}")
            
            # Add file content if requested
            if include_content:
                output_lines.append("")
                output_lines.append("**Content:**")
                output_lines.append("```" + language)
                content = self.get_file_content(file_path, max_token_count)
                output_lines.append(content)
                output_lines.append("```")
                
                # Count tokens
                content_tokens = self.count_tokens(content)
                file_token_counts[file_path] = content_tokens
            
            # Add separator
            output_lines.append("")
            output_lines.append("---")
            output_lines.append("")
        
        # Write to file
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write('\n'.join(output_lines))
        
        # Calculate statistics
        total_tokens = sum(file_token_counts.values()) if file_token_counts else 0
        
        return {
            'total_files': len(self.file_data),
            'total_tokens': total_tokens,
            'output_file': output_file
        }
    
    def language_stats(self) -> Dict[str, int]:
        """Get statistics of files by language.
        
        Returns:
            Dictionary with language stats
        """
        languages = {}
        for _, header_data in self.file_data.items():
            language = header_data['language']
            languages[language] = languages.get(language, 0) + 1
        
        return languages

def main():
    """Run the FORAI RepomiX generator."""
    parser = argparse.ArgumentParser(description='Generate a RepomiX-like file from FORAI headers')
    parser.add_argument('directory', help='Directory to scan for files with FORAI headers')
    parser.add_argument('--output', '-o', default='forai-repomix.md', help='Output file (default: forai-repomix.md)')
    parser.add_argument('--no-content', action='store_true', help='Exclude file content')
    parser.add_argument('--max-tokens', type=int, default=1000, help='Maximum tokens per file (default: 1000)')
    
    args = parser.parse_args()
    
    # Initialize and run RepomiX generator
    repomix = ForaiRepomiX()
    
    # Scan directory
    logger.info(f"Scanning directory: {args.directory}")
    scan_stats = repomix.scan_directory(args.directory)
    
    logger.info(f"Found {scan_stats['files_with_headers']} files with FORAI headers out of {scan_stats['total_files']} total files")
    for language, count in scan_stats['languages'].items():
        logger.info(f"  {language}: {count} files")
    
    # Generate RepomiX file
    logger.info(f"Generating RepomiX file: {args.output}")
    stats = repomix.generate_repomix(
        args.output,
        include_content=not args.no_content,
        max_token_count=args.max_tokens
    )
    
    logger.info(f"Generation complete: {stats['total_files']} files, {stats['total_tokens']} tokens")
    logger.info(f"Output written to: {stats['output_file']}")
    
    return 0

if __name__ == '__main__':
    sys.exit(main())