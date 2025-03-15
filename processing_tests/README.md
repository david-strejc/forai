# FORAI Multi-Language Extension

This directory contains code for extending FORAI (File Object Reference for AI Interpretation) to support multiple programming languages, with a specific focus on PHP and JavaScript.

## Overview

FORAI is a system for creating and maintaining machine-optimized headers in code files to help AI assistants understand code structure, dependencies, and relationships. This extension adds support for PHP and JavaScript files, using both regex-based and AST-based static analysis.

## Components

- **Language detection**: Automatically detects the programming language of a file
- **Static analyzers**: Parse PHP and JavaScript files to extract symbols, imports, and exports
- **Header generator**: Creates and inserts FORAI headers into files
- **Dependency resolver**: Resolves cross-file dependencies and updates headers
- **RepomiX generator**: Creates a comprehensive representation of the codebase similar to RepomiX

## Tools

- `language_detector.py`: Detects the programming language of a file
- `php_analyzer_stub.py`: Regex-based PHP analyzer (basic)
- `js_analyzer_stub.py`: Regex-based JavaScript analyzer (basic)
- `php_ast_parser.py`: AST-based PHP analyzer (advanced)
- `js_ast_parser.py`: AST-based JavaScript analyzer (advanced)
- `forai_extension_demo.py`: Demo of multi-language support
- `process_all_files.py`: Processes all files in a directory and adds FORAI headers
- `dependency_resolver.py`: Resolves and updates dependencies in FORAI headers
- `forai_repomix.py`: Generates a RepomiX-like file from FORAI headers

## Usage

### Adding FORAI headers to all files in a directory

```bash
python process_all_files.py path/to/directory
```

### Resolving dependencies between files

```bash
python dependency_resolver.py path/to/directory
```

### Generating a RepomiX-like file

```bash
python forai_repomix.py path/to/directory --output output.md
```

## FORAI Header Format

FORAI headers have the following format:

```
//FORAI:F123;DEF[C1:ClassName,F2:functionName];IMP[F456:C789,F789:*];EXP[C1,F2];LANG[language]//
```

Where:
- `F123` is the file ID
- `DEF[]` contains definitions of symbols in the file
- `IMP[]` contains imports from other files
- `EXP[]` contains exports from this file
- `LANG[]` indicates the programming language

## Requirements

- Python 3.6+
- PHP (for AST-based PHP parsing)
- Node.js (for AST-based JavaScript parsing)
- tiktoken (for token counting in repomix generation)

## Future Work

- Add support for more languages (Python, Ruby, Go, etc.)
- Enhance AST parsers to handle more complex code patterns
- Create visualization tools for dependency graphs
- Integrate with the core FORAI system