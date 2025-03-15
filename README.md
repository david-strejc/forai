# FORAI - File Object Reference for AI Interpretation

FORAI is a system that adds machine-readable metadata headers to source code files, enabling AI assistants to better understand, analyze, and navigate codebases. By embedding structured information directly in files, FORAI creates a self-documenting codebase optimized for AI interpretation.

## Table of Contents

- [Concept](#concept)
- [How It Works](#how-it-works)
- [Header Format](#header-format)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Tools](#tools)
- [Multi-language Support](#multi-language-support)
- [Technical Implementation](#technical-implementation)
- [Benefits](#benefits)
- [Limitations and Challenges](#limitations-and-challenges)
- [Future Development](#future-development)
- [Comparison with Other Tools](#comparison-with-other-tools)
- [Contributing](#contributing)
- [License](#license)

## Concept

AI assistants like Claude often struggle with large codebases due to limited context windows and a lack of global understanding of the codebase structure. FORAI addresses this by adding machine-readable headers to each file that encode:

1. **Definitions** - Classes, functions, and other symbols defined in the file
2. **Imports** - Dependencies and imported symbols from other files
3. **Exports** - Symbols exposed or exported by the file
4. **Language** - The programming language of the file
5. **Relationships** - How this file relates to other files in the project

This metadata helps AI assistants quickly understand the role of each file, its position in the dependency graph, and how to navigate between related files, all without needing to load the entire codebase into context.

## How It Works

FORAI adds specially formatted comment headers to source code files. For example:

```
//FORAI:F123;DEF[C1:ClassName,F2:functionName];IMP[F456:C789,F789:*];EXP[C1,F2];LANG[javascript]//
```

These headers are generated through static analysis of the codebase and inserted at the beginning of each file. The headers are compact and machine-readable, but don't interfere with the normal functioning of the code.

## Header Format

FORAI headers follow this format:

```
//FORAI:{file_id};DEF[{definitions}];IMP[{imports}];EXP[{exports}];LANG[{language}]//
```

Where:
- **file_id**: A unique identifier for the file (e.g., `F123`)
- **definitions**: List of symbols defined in the file, with their IDs and names (e.g., `C1:ClassName,F2:functionName`)
- **imports**: List of symbols imported from other files, with source file IDs and symbol IDs (e.g., `F456:C789,F789:*`)
- **exports**: List of symbol IDs exported by the file (e.g., `C1,F2`)
- **language**: The programming language of the file (e.g., `javascript`)

Additional details:

- **Symbol IDs**: Start with a letter indicating type (`C` for classes, `F` for functions, etc.) followed by a number
- **Parent Classes**: Can be specified with angle brackets (e.g., `C1:ClassName<C2>` indicates that ClassName inherits from the symbol with ID C2)
- **Wildcard Imports**: `*` indicates importing all exports from a file

## Features

- **Cross-file Navigation**: AI assistants can follow references between files
- **Symbol Tracking**: Unique identifiers for every class, function, and important symbol
- **Dependency Mapping**: Clear indication of inter-file dependencies
- **Multi-language Support**: Works with Python, JavaScript, PHP, and can be extended to other languages
- **Minimal Overhead**: Headers are compact and don't significantly impact file size
- **Self-documenting**: The codebase becomes more self-descriptive
- **Automated Generation**: Tools for automatically analyzing and adding headers to existing codebases
- **RepomiX Integration**: Generate AI-friendly documentation of the entire codebase

## Installation

```bash
# Clone the repository
git clone https://github.com/david-strejc/forai.git
cd forai

# Install dependencies (requires Python 3.6+)
pip install -r requirements.txt

# Optional dependencies for specific language support
# For PHP parsing
# apt-get install php
# For JavaScript parsing
# apt-get install nodejs
```

## Usage

### Adding FORAI Headers to a Project

```bash
# Process all files in a directory
python forai/processing_tests/process_all_files.py path/to/your/project

# Resolve cross-file dependencies
python forai/processing_tests/dependency_resolver.py path/to/your/project

# Generate a RepomiX-like documentation file
python forai/processing_tests/forai_repomix.py path/to/your/project --output project-repomix.md
```

### Analyzing a Single File

```bash
# Analyze a single file
python forai/processing_tests/forai_extension_demo.py path/to/your/file.py --update
```

### Testing FORAI on Sample Files

The repository includes sample files for testing FORAI capabilities:

```bash
# Test header generation on sample files
python forai/processing_tests/test_header_generation.py

# Test on different languages
python forai/processing_tests/test_foreign_languages.py
```

## Tools

FORAI includes several tools:

1. **Static Analyzers**: Language-specific tools for extracting symbols and dependencies
   - `php_analyzer_stub.py`: Regex-based PHP analyzer
   - `js_analyzer_stub.py`: Regex-based JavaScript analyzer
   - `php_ast_parser.py`: AST-based PHP analyzer
   - `js_ast_parser.py`: AST-based JavaScript analyzer
   - `ast_parsers.py`: Simplified AST parsers for PHP and JavaScript

2. **Header Generators**: Tools for creating and inserting FORAI headers
   - `forai_extension_demo.py`: Demonstrates multi-language header generation
   - `process_all_files.py`: Processes all files in a directory

3. **Dependency Management**: Tools for resolving cross-file dependencies
   - `dependency_resolver.py`: Resolves and updates dependencies between files

4. **Documentation Generation**: Tools for creating AI-friendly documentation
   - `forai_repomix.py`: Generates a RepomiX-like file from FORAI headers

5. **Utility Functions**: Helper tools and utilities
   - `language_detector.py`: Detects programming language of files

## Multi-language Support

FORAI currently supports:

- **Python**: Native support with full symbol extraction
- **JavaScript**: Support for ES6 modules, CommonJS, classes, functions
- **PHP**: Support for namespaces, classes, methods, functions

Each language has both regex-based and AST-based analyzers. The AST-based analyzers provide more accurate symbol extraction but may require additional dependencies.

### Language-specific Features

#### Python

- Detects classes, functions, imports, and exports
- Handles inheritance relationships
- Supports module-level exports

#### JavaScript

- Supports ES6 classes and inheritance
- Detects both function declarations and arrow functions
- Handles ES6 imports/exports and CommonJS require/module.exports
- Works with both browser and Node.js code

#### PHP

- Supports PHP classes and inheritance
- Handles namespace declarations
- Detects class methods and functions
- Works with use statements for imports

### Adding Support for New Languages

To add support for a new language:

1. Create a language detector rule in `language_detector.py`
2. Implement a static analyzer for the language (see `php_analyzer_stub.py` as an example)
3. Update the `MultiLanguageStaticAnalyzer` class in `forai_extension_demo.py`
4. Implement appropriate header insertion logic in `MultiLanguageHeaderGenerator`

## Technical Implementation

### Symbol Registry

The core of FORAI is the Symbol Registry, which:
- Generates unique IDs for files (e.g., `F123`)
- Generates unique IDs for symbols (e.g., `C1` for classes, `F2` for functions)
- Maintains a mapping between files, symbols, and their IDs

The registry ensures consistency across the entire codebase and enables cross-file references.

### Static Analysis

FORAI uses static analysis to extract information from source code:

1. **Regex-based Analysis**: Simple pattern matching for basic symbol extraction
   - Faster and has fewer dependencies
   - Less accurate for complex code patterns
   - Implemented in `php_analyzer_stub.py` and `js_analyzer_stub.py`

2. **AST-based Analysis**: More accurate analysis using language-specific AST parsers
   - More accurate for complex code patterns
   - Requires language-specific dependencies
   - Implemented in `php_ast_parser.py` and `js_ast_parser.py`

The analyzers extract:
- **Definitions**: Classes, functions, methods, variables (language-dependent)
- **Imports**: Import statements, require calls, use statements
- **Exports**: Export statements, public symbols, return values

### Header Generation and Insertion

Header generation follows these steps:

1. Detect the language of the file
2. Select appropriate analyzer based on language
3. Extract symbols, imports, and exports
4. Generate a FORAI header
5. Insert the header at the appropriate position based on language conventions

Language-specific insertion rules:
- **Python**: After shebang and encoding declarations
- **JavaScript**: After any initial comment blocks
- **PHP**: After the opening `<?php` tag

### Dependency Resolution

Dependency resolution works by:

1. Scanning all FORAI headers in the codebase
2. Building a map of exported symbols and their file IDs
3. Creating a mapping from import paths to file paths
4. Updating all import references to use proper file_id:symbol_id format

The `dependency_resolver.py` script performs these steps to create a coherent dependency graph across the codebase.

### RepomiX Integration

The `forai_repomix.py` tool creates an AI-friendly documentation file that:

1. Contains a full listing of all files with their FORAI headers
2. Organizes files by language
3. Shows file dependencies and relationships
4. Includes truncated file content (limited by token count)
5. Provides a comprehensive view of the codebase structure

This is similar to the RepomiX tool, but with FORAI-specific enhancements.

## Benefits

- **Enhanced AI Code Understanding**: Enables AI assistants to better understand code structure
- **Reduced Context Need**: AI can work with smaller context windows by following references
- **Improved Navigation**: Easy navigation between related files
- **Better Recommendations**: More accurate code completion and recommendations
- **Language Agnostic**: Works across multiple programming languages
- **Minimal Overhead**: Small file size impact, no runtime performance impact
- **Static Analysis**: No runtime dependencies or execution required
- **Open Format**: Simple text-based format that can be extended

## Limitations and Challenges

- **Maintenance**: Headers need to be updated when files change
  - Solution: IDE integrations and git hooks could automate this
  
- **Accuracy**: Static analysis may miss dynamic imports or complex patterns
  - Solution: AST-based parsers help, but some edge cases remain
  
- **Language Support**: Not all languages are supported yet
  - Solution: Modular design allows adding new languages
  
- **Tooling Integration**: Requires integration with development workflows
  - Solution: IDE plugins and CI/CD integrations planned
  
- **Adoption**: Requires developers to adopt and maintain the system
  - Solution: Show clear benefits and provide automation tools
  
- **Parse Errors**: Complex or non-standard code may cause parsing issues
  - Solution: Fallback mechanisms and error reporting
  
- **Large Codebases**: Performance may be slower on very large codebases
  - Solution: Incremental processing and parallel execution

## Future Development

Future plans for FORAI include:

### Short-term Goals

- **More Languages**: Add support for Ruby, Go, Rust, and other languages
- **IDE Integration**: Create plugins for VS Code, JetBrains IDEs
- **CI/CD Integration**: Automatic header generation and validation in CI pipelines
- **Improved AST Parsing**: More accurate symbol extraction
- **Better Performance**: Optimize for large codebases

### Mid-term Goals

- **Semantic Information**: Add type information, parameter details, and return types
- **Documentation Integration**: Extract and include docstrings and comments
- **Call Graph Analysis**: Track function calls and data flow
- **Visual Tools**: Create visualization tools for dependency graphs
- **Header Verification**: Tools to verify header accuracy

### Long-term Goals

- **Standardization**: Establish a standard format for AI-readable code metadata
- **Ecosystem Integration**: Integration with package managers and build systems
- **Runtime Reflection**: Optional runtime components for dynamic languages
- **Intelligent Caching**: Smart context management for AI assistants
- **Semantic Versioning**: Track symbol changes for better upgrade paths

### Feature Ideas

1. **FORAI Query Language**: A specialized query language for navigating code relationships
2. **AI Assistant Plugins**: Direct integrations with Claude, ChatGPT, etc.
3. **Code Generation Hooks**: Use FORAI headers to guide AI code generation
4. **File Selection Optimization**: Smart selection of files to include in context windows
5. **Dynamic Symbol Resolution**: Runtime component for more accurate dynamic analysis
6. **Semantic Diff**: Track symbol changes between versions
7. **Type Inference**: Add type information even for dynamically typed languages
8. **Cross-Repository Links**: Support references across multiple repositories

## Comparison with Other Tools

### FORAI vs. RepomiX

- **RepomiX**: Creates a single consolidated file representing the entire codebase
  - Pros: Simple, works with any AI assistant
  - Cons: No direct file navigation, requires regeneration
  
- **FORAI**: Adds metadata headers directly to source files, enabling direct navigation
  - Pros: Enables precise navigation, always up-to-date
  - Cons: Requires AI understanding of the format

### FORAI vs. Source Code Indexers

- **Indexers** (e.g., Ctags): Create external index files for navigating code
  - Pros: Well-established, IDE integration
  - Cons: Separate from code, not AI-optimized
  
- **FORAI**: Embeds metadata directly in source files, making it available to AI
  - Pros: Self-contained, AI-optimized format
  - Cons: Newer approach, less tool support

### FORAI vs. Language Servers

- **Language Servers**: Provide real-time code information to IDEs
  - Pros: Rich semantic information, real-time
  - Cons: Complex, requires running a server
  
- **FORAI**: Provides static metadata optimized for AI comprehension
  - Pros: Simple, no server needed, cross-language
  - Cons: Less detailed semantic information

### FORAI vs. Documentation Tools

- **Documentation Tools**: Generate human-readable documentation
  - Pros: Rich human-readable format
  - Cons: Not optimized for machine processing
  
- **FORAI**: Creates machine-readable metadata for AI consumption
  - Pros: Compact, machine-optimized
  - Cons: Not as human-friendly

## Contributing

Contributions are welcome! Here's how you can help:

1. **New Language Support**: Add analyzers for additional languages
2. **Improve Parsers**: Enhance the accuracy of existing analyzers
3. **Documentation**: Improve documentation and examples
4. **Testing**: Add tests and improve test coverage
5. **Integration**: Create integrations with other tools and systems

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.