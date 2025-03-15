#!/usr/bin/env python3
"""
FORAI Header Generation Test

This script tests the generation of FORAI headers for PHP and JavaScript files
from the ESPOcrm codebase.
"""

import os
import sys
import random
import logging
from pathlib import Path
import tempfile
import shutil

# Import utilities
from language_detector import detect_language
from php_analyzer_stub import PHPStaticAnalyzer, MockSymbolRegistry
from js_analyzer_stub import JavaScriptStaticAnalyzer
from forai_extension_demo import MultiLanguageHeaderGenerator

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def select_sample_files(base_dir, file_types=None, count=5):
    """
    Select a random sample of files from a directory.
    
    Args:
        base_dir: The base directory to search for files
        file_types: List of file extensions to include (e.g., ['.php', '.js'])
        count: Number of files to select per file type
        
    Returns:
        A dictionary of {file_type: [file_paths]}
    """
    if file_types is None:
        file_types = ['.php', '.js', '.py']
        
    file_type_map = {ext: [] for ext in file_types}
    
    # Walk through directory tree
    for root, _, files in os.walk(base_dir):
        for file in files:
            file_path = os.path.join(root, file)
            file_ext = os.path.splitext(file)[1].lower()
            
            if file_ext in file_types:
                file_type_map[file_ext].append(file_path)
    
    # Select random samples for each file type
    samples = {}
    for file_type, files in file_type_map.items():
        if files:
            sample_count = min(count, len(files))
            samples[file_type] = random.sample(files, sample_count)
        else:
            samples[file_type] = []
    
    return samples

def add_headers_to_samples(samples):
    """
    Add FORAI headers to sample files.
    
    Args:
        samples: Dictionary of {file_type: [file_paths]}
        
    Returns:
        Number of files successfully processed
    """
    # Initialize components
    registry = MockSymbolRegistry()
    php_analyzer = PHPStaticAnalyzer(registry)
    js_analyzer = JavaScriptStaticAnalyzer(registry)
    header_generator = MultiLanguageHeaderGenerator(registry)
    
    success_count = 0
    
    # Process each file
    for file_type, files in samples.items():
        logger.info(f"Processing {len(files)} {file_type} files")
        
        for file_path in files:
            try:
                # Detect language
                language = detect_language(file_path)
                
                # Analyze file
                if language == 'php':
                    file_data = php_analyzer.analyze_file(file_path)
                elif language == 'javascript':
                    file_data = js_analyzer.analyze_file(file_path)
                else:
                    # Skip unknown languages
                    logger.warning(f"Skipping unsupported language: {language} for {file_path}")
                    continue
                
                # Add language information
                file_data['language'] = language
                
                # Generate header
                header = header_generator.generate_header(file_data)
                
                # Update file
                header_generator.update_file_header(file_path, header)
                
                success_count += 1
                logger.info(f"Added header to {file_path}")
                
                # Read the first few lines to confirm header was added
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    first_lines = ''.join([f.readline() for _ in range(10)])
                    if '//FORAI:' in first_lines:
                        logger.info("Header confirmed in file")
                    else:
                        logger.warning("Header not found in file after adding")
                
            except Exception as e:
                logger.error(f"Error processing {file_path}: {e}")
    
    return success_count

def test_header_reading(samples):
    """
    Test reading FORAI headers from files.
    
    Args:
        samples: Dictionary of {file_type: [file_paths]}
        
    Returns:
        Number of files with valid headers
    """
    valid_headers = 0
    
    for file_type, files in samples.items():
        logger.info(f"Reading headers from {len(files)} {file_type} files")
        
        for file_path in files:
            try:
                # Read file
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read(2000)  # Read first 2000 characters
                
                # Look for header
                import re
                header_match = re.search(r'//FORAI:(.*?)//', content)
                
                if header_match:
                    header = header_match.group(0)
                    logger.info(f"Found header in {file_path}: {header}")
                    valid_headers += 1
                else:
                    logger.warning(f"No header found in {file_path}")
                
            except Exception as e:
                logger.error(f"Error reading {file_path}: {e}")
    
    return valid_headers

def clean_codebase(base_dir, output_dir):
    """
    Create a cleaned copy of the codebase with only PHP and JS files.
    
    Args:
        base_dir: Base directory of the original codebase
        output_dir: Output directory for the cleaned codebase
        
    Returns:
        Tuple of (php_file_count, js_file_count)
    """
    php_count = 0
    js_count = 0
    
    # Create output directory structure
    os.makedirs(output_dir, exist_ok=True)
    
    # Copy only PHP and JS files
    for root, dirs, files in os.walk(base_dir):
        # Skip unwanted directories
        if any(d in root for d in ['node_modules', 'vendor', '.git']):
            continue
            
        for file in files:
            file_ext = os.path.splitext(file)[1].lower()
            
            if file_ext in ['.php', '.js']:
                # Create relative path
                rel_path = os.path.relpath(os.path.join(root, file), base_dir)
                dst_path = os.path.join(output_dir, rel_path)
                
                # Create directory structure
                os.makedirs(os.path.dirname(dst_path), exist_ok=True)
                
                # Copy file
                shutil.copy2(os.path.join(root, file), dst_path)
                
                if file_ext == '.php':
                    php_count += 1
                elif file_ext == '.js':
                    js_count += 1
    
    return php_count, js_count

def main():
    """Run the FORAI header generation test."""
    if len(sys.argv) > 1:
        base_dir = sys.argv[1]
    else:
        base_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'espocrm')
    
    cleaned_dir = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'cleaned_files')
    
    # Clean the codebase
    logger.info(f"Cleaning codebase from {base_dir} to {cleaned_dir}")
    php_count, js_count = clean_codebase(base_dir, cleaned_dir)
    logger.info(f"Copied {php_count} PHP files and {js_count} JS files to cleaned directory")
    
    # Select sample files from cleaned directory
    logger.info("Selecting sample files")
    samples = select_sample_files(cleaned_dir, file_types=['.php', '.js'], count=10)
    
    # Print sample files
    for file_type, files in samples.items():
        logger.info(f"Selected {len(files)} {file_type} files")
        for file in files:
            logger.info(f"  {file}")
    
    # Add headers to sample files
    logger.info("Adding headers to sample files")
    success_count = add_headers_to_samples(samples)
    logger.info(f"Added headers to {success_count} files")
    
    # Test reading headers
    logger.info("Testing header reading")
    valid_headers = test_header_reading(samples)
    logger.info(f"Found valid headers in {valid_headers} files")
    
    # Summary
    logger.info("\n===== Summary =====")
    logger.info(f"Total PHP files: {php_count}")
    logger.info(f"Total JS files: {js_count}")
    logger.info(f"Samples processed: {sum(len(files) for files in samples.values())}")
    logger.info(f"Headers added: {success_count}")
    logger.info(f"Valid headers found: {valid_headers}")
    
    return 0

if __name__ == '__main__':
    sys.exit(main())