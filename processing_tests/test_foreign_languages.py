#!/usr/bin/env python3
"""Test script for FORAI on foreign language files (PHP and JavaScript).

This script tests how FORAI handles PHP and JavaScript files which 
are not natively supported in the current implementation.
"""

import os
import sys
import subprocess
import json
from pathlib import Path

def run_command(cmd):
    """Run a command and print the output."""
    print(f"\n> {' '.join(cmd)}")
    result = subprocess.run(cmd, capture_output=True, text=True)
    
    if result.returncode == 0:
        try:
            output = json.loads(result.stdout)
            print(json.dumps(output, indent=2))
        except json.JSONDecodeError:
            print(result.stdout)
    else:
        print(f"Error: {result.stderr}")
    
    return result.returncode == 0

def count_files_by_type(directory):
    """Count files by extension in a directory."""
    file_counts = {}
    
    for root, _, files in os.walk(directory):
        for file in files:
            ext = os.path.splitext(file)[1].lower()
            if ext:
                file_counts[ext] = file_counts.get(ext, 0) + 1
    
    return file_counts

def main():
    """Run the FORAI test on foreign language files."""
    # Get project directory
    project_dir = Path(__file__).parent / "espocrm_sample"
    
    # Print file statistics
    print("=== File Statistics ===")
    file_counts = count_files_by_type(project_dir)
    for ext, count in sorted(file_counts.items(), key=lambda x: x[1], reverse=True):
        print(f"{ext}: {count} files")
    
    # Ensure we have a clean start
    registry_dir = project_dir / ".forai"
    if registry_dir.exists():
        import shutil
        shutil.rmtree(registry_dir)
    
    # Try to generate FORAI headers for all files
    print("\n=== Attempting to Generate FORAI Headers ===")
    result = run_command([
        "forai",
        "--workspace", str(project_dir),
        "update-all"
    ])
    
    # Check a sample PHP file
    print("\n=== Sample PHP File Check ===")
    php_files = list(project_dir.glob("**/*.php"))
    if php_files:
        sample_php = php_files[0]
        print(f"Checking {sample_php}...")
        
        # Read the file content
        with open(sample_php, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(500)  # Read first 500 characters
            print("\nFile content (first 500 chars):")
            print(content)
        
        # Try to update a single PHP file
        print("\nTrying to update a single PHP file...")
        run_command([
            "forai",
            "--workspace", str(project_dir),
            "update", str(sample_php)
        ])
    
    # Check a sample JavaScript file
    print("\n=== Sample JavaScript File Check ===")
    js_files = list(project_dir.glob("**/*.js"))
    if js_files:
        sample_js = js_files[0]
        print(f"Checking {sample_js}...")
        
        # Read the file content
        with open(sample_js, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(500)  # Read first 500 characters
            print("\nFile content (first 500 chars):")
            print(content)
        
        # Try to update a single JavaScript file
        print("\nTrying to update a single JavaScript file...")
        run_command([
            "forai",
            "--workspace", str(project_dir),
            "update", str(sample_js)
        ])
    
    # Show summary
    print("\n=== Test Summary ===")
    print("This test demonstrates how FORAI currently handles unsupported languages.")
    print("The current implementation is focused on Python files, but the")
    print("architecture is designed to be extended to support other languages.")
    print("\nTo add support for PHP and JavaScript, you would need to:")
    print("1. Create language-specific AST parsers (php-ast, esprima, etc.)")
    print("2. Add language detection in the static analyzer")
    print("3. Implement language-specific symbol extractors")
    print("4. Update the header generator to handle language-specific patterns")

if __name__ == "__main__":
    main()