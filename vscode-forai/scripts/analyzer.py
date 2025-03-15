#!/usr/bin/env python3
"""
FORAI analyzer script for VSCode extension.

This script is a wrapper around the forai CLI that can be called from the VSCode extension.
"""

import argparse
import sys
import json
import os
import subprocess

def main():
    """Main entry point."""
    parser = argparse.ArgumentParser(description='FORAI Analyzer Script')
    parser.add_argument('--file', required=True, help='Path to file or --all')
    parser.add_argument('--workspace', required=True, help='Path to workspace root')
    parser.add_argument('--runtime', action='store_true', help='Enable runtime introspection')
    parser.add_argument('--rename', action='store_true', help='Handle file rename')
    parser.add_argument('--old-path', help='Previous file path (for rename)')
    parser.add_argument('--new-path', help='New file path (for rename)')
    parser.add_argument('--update-deps', action='store_true', help='Update dependent files')
    
    args = parser.parse_args()
    
    try:
        # Build forai CLI command
        cmd = ['python', '-m', 'forai', '--workspace', args.workspace]
        
        if args.runtime:
            cmd.append('--runtime')
            
        if args.file == '--all':
            cmd.append('update-all')
        elif args.rename:
            if not args.old_path or not args.new_path:
                raise ValueError("--old-path and --new-path required for rename operation")
                
            cmd.extend(['rename', args.old_path, args.new_path])
        elif args.update_deps:
            cmd.extend(['update-deps', args.file])
        else:
            cmd.extend(['update', args.file])
            
        # Run forai CLI
        result = subprocess.run(cmd, capture_output=True, text=True)
        
        if result.returncode == 0:
            # Parse JSON output
            try:
                output = json.loads(result.stdout)
                print(json.dumps(output))
            except json.JSONDecodeError:
                print(json.dumps({
                    'success': False,
                    'error': f"Failed to parse FORAI output: {result.stdout}"
                }))
        else:
            print(json.dumps({
                'success': False,
                'error': result.stderr or f"FORAI exited with code {result.returncode}"
            }))
            
    except Exception as e:
        print(json.dumps({
            'success': False,
            'error': str(e)
        }), file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    main()