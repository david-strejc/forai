#!/usr/bin/env python3
"""
FORAI Extension Demo

This script demonstrates how FORAI can be extended to support multiple languages.
"""

import os
import sys
import json
import logging
import importlib
from pathlib import Path

# Import language detection utility
from language_detector import detect_language

# Import necessary registry
from php_analyzer_stub import MockSymbolRegistry

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class MultiLanguageStaticAnalyzer:
    """Static analyzer that supports multiple languages."""
    
    def __init__(self, symbol_registry):
        """Initialize the multi-language static analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        self.analyzers = {}
        
        # Try to load AST-based analyzers first, then fall back to regex-based ones
        
        # Try to use simplified AST parsers
        try:
            ast_parsers = importlib.import_module('ast_parsers')
            self.analyzers['php'] = ast_parsers.PHPAstAnalyzer(self.registry)
            self.analyzers['javascript'] = ast_parsers.JavaScriptAstAnalyzer(self.registry)
            logger.info("Using simplified AST-based parsers")
        except ImportError:
            # Fall back to regex-based analyzers
            php_module = importlib.import_module('php_analyzer_stub')
            self.analyzers['php'] = php_module.PHPStaticAnalyzer(self.registry)
            js_module = importlib.import_module('js_analyzer_stub')
            self.analyzers['javascript'] = js_module.JavaScriptStaticAnalyzer(self.registry)
            logger.info("Using regex-based parsers")
        
        # Python analyzer would be imported from forai.static_analyzer
        self.analyzers['python'] = None
        
    def analyze_file(self, file_path):
        """Analyze a file statically.
        
        Args:
            file_path: Path to the file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Detect language
        language = detect_language(file_path)
        
        # Select appropriate analyzer
        if language in self.analyzers and self.analyzers[language] is not None:
            result = self.analyzers[language].analyze_file(file_path)
            result['language'] = language
            return result
        else:
            logger.warning(f"No analyzer available for language: {language}")
            return {
                'file_id': self.registry.get_file_id(file_path),
                'definitions': [],
                'imports': [],
                'exports': [],
                'language': language,
                'error': f'Unsupported language or analyzer not initialized: {language}'
            }

class MultiLanguageHeaderGenerator:
    """Header generator that supports multiple languages."""
    
    def __init__(self, symbol_registry):
        """Initialize the multi-language header generator.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        
    def generate_header(self, file_data):
        """Generate a FORAI header from file analysis data.
        
        Args:
            file_data: A dictionary with file_id, definitions, imports, and exports
            
        Returns:
            The FORAI header string
        """
        file_id = file_data.get('file_id', '')
        language = file_data.get('language', 'unknown')
        
        # Format definitions
        def_parts = []
        for defn in file_data.get('definitions', []):
            symbol_id = defn.get('symbol_id', '')
            name = defn.get('name', '')
            parents = defn.get('parents', [])
            
            if not symbol_id or not name:
                continue
                
            if parents:
                parent_refs = '<' + ','.join(parents) + '>'
                def_parts.append(f"{symbol_id}:{name}{parent_refs}")
            else:
                def_parts.append(f"{symbol_id}:{name}")
        
        # Format imports
        imp_parts = []
        for imp in file_data.get('imports', []):
            file_ref = imp.get('file_id', '')
            symbol_ref = imp.get('symbol_id', '*')
            
            if not file_ref:
                continue
                
            imp_parts.append(f"{file_ref}:{symbol_ref}")
        
        # Format exports
        exp_parts = file_data.get('exports', [])
        
        # Build header
        header = f"//FORAI:{file_id};"
        
        if def_parts:
            header += f"DEF[{','.join(def_parts)}];"
        else:
            header += "DEF[];"
            
        if imp_parts:
            header += f"IMP[{','.join(imp_parts)}];"
        else:
            header += "IMP[];"
            
        if exp_parts:
            header += f"EXP[{','.join(exp_parts)}];"
        else:
            header += "EXP[];"
            
        # Add language information
        header += f"LANG[{language}]//"
        
        return header
    
    def update_file_header(self, file_path, header):
        """Update the FORAI header in a file.
        
        Args:
            file_path: Path to the file
            header: The FORAI header to add or update
        """
        language = detect_language(file_path)
        
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
        except Exception as e:
            print(f"Failed to read file {file_path}: {e}")
            return
        
        # Check if header already exists
        header_pattern = r'//FORAI:.*?//'
        import re
        
        if re.search(header_pattern, content):
            # Replace existing header
            updated_content = re.sub(header_pattern, header, content)
        else:
            # Add header at the top, according to language conventions
            if language == 'python':
                # Preserve shebang and encoding declaration
                lines = content.splitlines()
                insert_pos = 0
                
                # Skip shebang line
                if lines and lines[0].startswith('#!'):
                    insert_pos = 1
                    
                # Skip encoding declaration
                if len(lines) > insert_pos and re.match(r'#.*coding[:=]', lines[insert_pos]):
                    insert_pos += 1
                    
                lines.insert(insert_pos, header)
                updated_content = '\n'.join(lines)
                
            elif language == 'php':
                # For PHP, insert after the opening <?php tag
                match = re.search(r'<\?php', content)
                if match:
                    pos = match.end()
                    updated_content = content[:pos] + '\n' + header + '\n' + content[pos:]
                else:
                    updated_content = header + '\n' + content
                    
            elif language == 'javascript':
                # For JavaScript, insert after any initial comment block
                comment_end = 0
                if content.startswith('/*'):
                    # Handle multi-line comment block
                    end_pos = content.find('*/') 
                    if end_pos > 0:
                        comment_end = end_pos + 2
                        # Skip any empty lines after the comment block
                        newline_pos = content.find('\n', comment_end)
                        while newline_pos > 0 and content[comment_end:newline_pos].strip() == '':
                            comment_end = newline_pos + 1
                            newline_pos = content.find('\n', comment_end)
                elif content.startswith('//'):
                    # Handle single-line comment block
                    lines = content.splitlines()
                    in_comment_block = True
                    for i, line in enumerate(lines):
                        line_strip = line.strip()
                        # If we find a non-comment, non-empty line, end the comment block
                        if not line_strip.startswith('//') and line_strip:
                            in_comment_block = False
                            comment_end = sum(len(line) + 1 for line in lines[:i])
                            break
                    
                    # If we're still in a comment block (all lines are comments or empty)
                    if in_comment_block:
                        # Find the first empty line after the comments
                        for i, line in enumerate(lines):
                            if not line.strip().startswith('//') and not line.strip():
                                comment_end = sum(len(line) + 1 for line in lines[:i+1])
                                break
                        # If no empty line found and there are lines, put header after all content
                        if comment_end == 0 and lines:
                            comment_end = sum(len(line) + 1 for line in lines)
                
                # Insert the header
                if comment_end > 0:
                    # Insert header after comment block with proper spacing
                    updated_content = content[:comment_end] + '\n' + header + '\n\n' + content[comment_end:]
                else:
                    # Insert at the top if no comment block
                    updated_content = header + '\n\n' + content
                    
            else:
                # Default: insert at the top
                updated_content = header + '\n' + content
        
        # Write back to file
        try:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(updated_content)
        except Exception as e:
            print(f"Failed to write file {file_path}: {e}")
            return

def main():
    """Run the FORAI extension demo."""
    if len(sys.argv) < 2:
        print("Usage: forai_extension_demo.py <file>")
        return 1
    
    file_path = os.path.abspath(sys.argv[1])
    if not os.path.isfile(file_path):
        print(f"Error: {file_path} is not a valid file")
        return 1
    
    # Detect language
    language = detect_language(file_path)
    print(f"Detected language: {language}")
    
    # Initialize mock registry
    registry = MockSymbolRegistry()
    
    # Initialize analyzers
    analyzer = MultiLanguageStaticAnalyzer(registry)
    header_generator = MultiLanguageHeaderGenerator(registry)
    
    # Analyze file
    try:
        file_data = analyzer.analyze_file(file_path)
        print("\nAnalysis result:")
        print(json.dumps(file_data, indent=2))
        
        # Generate header
        header = header_generator.generate_header(file_data)
        print("\nGenerated header:")
        print(header)
        
        # Update file header
        header_generator.update_file_header(file_path, header)
        print("\nHeader added to file.")
        
        # Read the file to confirm header was added
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(500)  # Read first 500 characters
            print("\nFile content (first 500 chars):")
            print(content)
    except Exception as e:
        print(f"Error: {e}")
        return 1
    
    return 0

if __name__ == '__main__':
    sys.exit(main())