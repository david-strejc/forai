#!/usr/bin/env python3
"""Demo script for FORAI.

This script demonstrates how FORAI can be used to analyze and generate headers for Python files.
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

def main():
    """Run the FORAI demo."""
    # Get project directory
    project_dir = Path(__file__).parent / "sample_project"
    
    # Ensure we have a clean start
    registry_dir = project_dir / ".forai"
    if registry_dir.exists():
        import shutil
        shutil.rmtree(registry_dir)
    
    # Generate FORAI headers for all Python files
    print("=== Generating FORAI Headers ===")
    run_command([
        "forai",
        "--workspace", str(project_dir),
        "update-all"
    ])
    
    # Show generated headers
    print("\n=== Generated Headers ===")
    for file in ["base.py", "user.py", "admin.py", "main.py"]:
        file_path = project_dir / file
        print(f"\n--- {file} ---")
        with open(file_path, 'r') as f:
            content = f.read(500)  # Read first 500 characters to get the header
            header = content.split("\n")[0]
            print(header)
    
    # Query the FORAI headers
    print("\n=== Querying FORAI Headers ===")
    
    # Find where User is defined
    print("\n--- Find where User is defined ---")
    run_command([
        "forai-query",
        "--workspace", str(project_dir),
        "find", "User"
    ])
    
    # Get all symbols defined in user.py
    print("\n--- Get all symbols defined in user.py ---")
    run_command([
        "forai-query",
        "--workspace", str(project_dir),
        "file-symbols", str(project_dir / "user.py")
    ])
    
    # Find all files that use Logger
    print("\n--- Find all files that use Logger ---")
    run_command([
        "forai-query",
        "--workspace", str(project_dir),
        "usages", "Logger"
    ])
    
    # List all symbols in the workspace
    print("\n--- List all symbols in the workspace ---")
    run_command([
        "forai-query",
        "--workspace", str(project_dir),
        "list"
    ])
    
    # Make a change to user.py
    print("\n=== Making a change to user.py ===")
    user_path = project_dir / "user.py"
    with open(user_path, 'r') as f:
        content = f.read()
    
    # Add a new method to User class
    new_content = content.replace(
        "    def validate(self):",
        """    def get_full_info(self):
        """\"Get the user's full information.\"\"\"
        return {
            'id': self.id,
            'name': self.name,
            'email': self.email
        }
        
    def validate(self):"""
    )
    
    with open(user_path, 'w') as f:
        f.write(new_content)
    
    # Update the header
    print("\n--- Updating the header for user.py ---")
    run_command([
        "forai",
        "--workspace", str(project_dir),
        "update", str(user_path)
    ])
    
    # Show updated headers
    print("\n=== Updated Headers ===")
    for file in ["user.py", "admin.py"]:
        file_path = project_dir / file
        print(f"\n--- {file} ---")
        with open(file_path, 'r') as f:
            content = f.read(500)  # Read first 500 characters to get the header
            header = content.split("\n")[0]
            print(header)

if __name__ == "__main__":
    main()