#!/usr/bin/env python3
"""
Language Detection Utility for FORAI.

This script demonstrates how FORAI could be extended to detect and handle 
different programming languages.
"""

import os
import sys
import json

def detect_language(file_path):
    """Detect the language of a file based on extension and content."""
    ext = os.path.splitext(file_path)[1].lower()
    
    if ext == '.py':
        return 'python'
    elif ext == '.php':
        return 'php'
    elif ext == '.js':
        return 'javascript'
    else:
        # Try to detect based on content
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read(1000)  # Read first 1000 characters
                
                if '<?php' in content:
                    return 'php'
                elif 'function' in content and ('var ' in content or 'const ' in content or 'let ' in content):
                    return 'javascript'
                elif 'def ' in content and 'import ' in content:
                    return 'python'
        except:
            pass
                
    return 'unknown'

def main():
    """Run language detection on provided files."""
    if len(sys.argv) < 2:
        print("Usage: language_detector.py <file1> [file2 ...]")
        return 1
    
    results = {}
    for file_path in sys.argv[1:]:
        if os.path.isfile(file_path):
            language = detect_language(file_path)
            results[file_path] = language
    
    print(json.dumps(results, indent=2))
    return 0

if __name__ == '__main__':
    sys.exit(main())