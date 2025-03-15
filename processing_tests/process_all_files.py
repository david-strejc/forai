#!/usr/bin/env python3
"""
FORAI Process All Files

This script adds FORAI headers to all PHP and JavaScript files in a directory.
"""

import os
import sys
import logging
import importlib
from pathlib import Path
import concurrent.futures
import time

# Import utilities
from language_detector import detect_language
from php_analyzer_stub import MockSymbolRegistry
from forai_extension_demo import MultiLanguageStaticAnalyzer, MultiLanguageHeaderGenerator

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

def process_file(file_path, multi_language_analyzer, header_generator):
    """
    Process a single file by adding a FORAI header.
    
    Args:
        file_path: Path to the file
        multi_language_analyzer: Multi-language analyzer
        header_generator: Header generator
        
    Returns:
        Tuple of (success, language)
    """
    try:
        # Detect language
        language = detect_language(file_path)
        
        # Skip unsupported languages
        if language not in ['php', 'javascript']:
            logger.warning(f"Skipping unsupported language: {language} for {file_path}")
            return False, language
        
        # Analyze file
        file_data = multi_language_analyzer.analyze_file(file_path)
        
        # Check for analysis errors
        if 'error' in file_data:
            logger.warning(f"Error analyzing {file_path}: {file_data['error']}")
            return False, language
        
        # Generate header
        header = header_generator.generate_header(file_data)
        
        # Update file
        header_generator.update_file_header(file_path, header)
        
        # Verify header was added
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(2000)  # Read first 2000 characters to ensure we capture headers
            if '//FORAI:' in content:
                return True, language
            else:
                logger.warning(f"Header not found in {file_path} after adding")
                return False, language
                
    except Exception as e:
        logger.error(f"Error processing {file_path}: {e}")
        import traceback
        traceback.print_exc()
        return False, language

def process_all_files(directory, max_workers=4):
    """
    Process all PHP and JavaScript files in a directory.
    
    Args:
        directory: Directory containing the files
        max_workers: Maximum number of worker threads
        
    Returns:
        Dictionary with statistics
    """
    # Get all PHP and JS files
    php_files = []
    js_files = []
    
    for root, _, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            file_ext = os.path.splitext(file)[1].lower()
            
            if file_ext == '.php':
                php_files.append(file_path)
            elif file_ext == '.js':
                js_files.append(file_path)
    
    logger.info(f"Found {len(php_files)} PHP files and {len(js_files)} JavaScript files")
    
    # Initialize components
    registry = MockSymbolRegistry()
    analyzer = MultiLanguageStaticAnalyzer(registry)
    header_generator = MultiLanguageHeaderGenerator(registry)
    
    # Process files
    total_files = len(php_files) + len(js_files)
    processed_files = 0
    success_count = 0
    php_success = 0
    js_success = 0
    start_time = time.time()
    
    with concurrent.futures.ThreadPoolExecutor(max_workers=max_workers) as executor:
        # Submit all files for processing
        future_to_file = {
            executor.submit(process_file, file_path, analyzer, header_generator): file_path
            for file_path in php_files + js_files
        }
        
        # Process results as they complete
        for future in concurrent.futures.as_completed(future_to_file):
            file_path = future_to_file[future]
            processed_files += 1
            
            try:
                success, language = future.result()
                
                if success:
                    success_count += 1
                    if language == 'php':
                        php_success += 1
                    elif language == 'javascript':
                        js_success += 1
                
                # Print progress
                elapsed_time = time.time() - start_time
                files_per_second = processed_files / elapsed_time if elapsed_time > 0 else 0
                remaining_files = total_files - processed_files
                estimated_time = remaining_files / files_per_second if files_per_second > 0 else 0
                
                logger.info(f"Processed {processed_files}/{total_files} files "
                           f"({processed_files/total_files*100:.1f}%) - "
                           f"{files_per_second:.2f} files/sec - "
                           f"ETA: {estimated_time:.0f} seconds")
                
            except Exception as e:
                logger.error(f"Error processing {file_path}: {e}")
    
    # Calculate statistics
    end_time = time.time()
    elapsed_time = end_time - start_time
    stats = {
        'total_files': total_files,
        'php_files': len(php_files),
        'js_files': len(js_files),
        'success_count': success_count,
        'php_success': php_success,
        'js_success': js_success,
        'elapsed_time': elapsed_time,
        'files_per_second': total_files / elapsed_time if elapsed_time > 0 else 0
    }
    
    return stats

def main():
    """Process all files in a directory."""
    if len(sys.argv) > 1:
        directory = sys.argv[1]
    else:
        directory = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'cleaned_files')
    
    if not os.path.isdir(directory):
        logger.error(f"Directory not found: {directory}")
        return 1
    
    # Process all files
    logger.info(f"Processing all files in {directory}")
    stats = process_all_files(directory)
    
    # Print summary
    logger.info("\n===== Summary =====")
    logger.info(f"Total files processed: {stats['total_files']}")
    logger.info(f"  PHP files: {stats['php_files']}")
    logger.info(f"  JS files: {stats['js_files']}")
    logger.info(f"Files with headers added: {stats['success_count']} "
               f"({stats['success_count']/stats['total_files']*100:.1f}%)")
    logger.info(f"  PHP files: {stats['php_success']} "
               f"({stats['php_success']/stats['php_files']*100:.1f}%)")
    logger.info(f"  JS files: {stats['js_success']} "
               f"({stats['js_success']/stats['js_files']*100:.1f}%)")
    logger.info(f"Elapsed time: {stats['elapsed_time']:.2f} seconds")
    logger.info(f"Files per second: {stats['files_per_second']:.2f}")
    
    return 0

if __name__ == '__main__':
    sys.exit(main())