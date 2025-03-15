# FORAI Extension Plan for PHP and JavaScript Support

This document outlines how FORAI can be extended to support PHP and JavaScript files in addition to Python.

## Current Architecture

The current FORAI system is designed with Python in mind, using the following components:

1. **Static Analyzer**: Uses Python's AST module to parse Python files
2. **Symbol Registry**: Language-agnostic storage of symbols and file information
3. **Header Generator**: Generates and updates FORAI headers in Python files
4. **Dependency Tracker**: Language-agnostic tracking of dependencies between files

## Extension Requirements

To support PHP and JavaScript, we need to extend the following components:

1. **Static Analyzer**: Add language-specific parsers for PHP and JavaScript
2. **Header Generator**: Update to handle language-specific header insertion patterns
3. **CLI Tools**: Update to detect file types and apply appropriate analyzers

## Extension Plan

### 1. Language Detection

Add a language detection module to determine the language based on file extension and content:

```python
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
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read(1000)  # Read first 1000 characters
            
            if '<?php' in content:
                return 'php'
            elif 'function' in content and ('var ' in content or 'const ' in content or 'let ' in content):
                return 'javascript'
            elif 'def ' in content and 'import ' in content:
                return 'python'
                
        return 'unknown'
```

### 2. PHP Parser Integration

Integrate with PHP-Parser (a PHP parser written in PHP) or php-ast (a PHP extension for AST):

1. Create a PHP adapter that uses subprocess to call a PHP script with PHP-Parser
2. Define PHP-specific AST visitors for extracting symbols

```python
class PHPStaticAnalyzer:
    """Static analyzer for PHP files."""
    
    def __init__(self, symbol_registry):
        self.registry = symbol_registry
        
    def analyze_file(self, file_path):
        """Analyze a PHP file statically."""
        # Call PHP parser in a subprocess
        result = subprocess.run([
            'php', 'php_parser.php', file_path
        ], capture_output=True, text=True)
        
        if result.returncode != 0:
            raise Exception(f"Failed to parse PHP file: {result.stderr}")
            
        # Parse JSON output from PHP parser
        parse_result = json.loads(result.stdout)
        
        # Extract symbols
        file_id = self.registry.get_file_id(file_path)
        imports = self._extract_imports(parse_result['imports'])
        definitions = self._extract_definitions(file_id, parse_result['definitions'])
        exports = self._extract_exports(definitions, parse_result['exports'])
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
```

### 3. JavaScript Parser Integration

Integrate with Esprima or Acorn (popular JavaScript parsers):

1. Use Node.js to execute a JavaScript parser
2. Define JavaScript-specific AST visitors for extracting symbols

```python
class JavaScriptStaticAnalyzer:
    """Static analyzer for JavaScript files."""
    
    def __init__(self, symbol_registry):
        self.registry = symbol_registry
        
    def analyze_file(self, file_path):
        """Analyze a JavaScript file statically."""
        # Call JavaScript parser in a subprocess
        result = subprocess.run([
            'node', 'js_parser.js', file_path
        ], capture_output=True, text=True)
        
        if result.returncode != 0:
            raise Exception(f"Failed to parse JavaScript file: {result.stderr}")
            
        # Parse JSON output from JavaScript parser
        parse_result = json.loads(result.stdout)
        
        # Extract symbols
        file_id = self.registry.get_file_id(file_path)
        imports = self._extract_imports(parse_result['imports'])
        definitions = self._extract_definitions(file_id, parse_result['definitions'])
        exports = self._extract_exports(definitions, parse_result['exports'])
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
```

### 4. Update Header Generator

Extend the header generator to handle language-specific comment formats:

```python
class HeaderGenerator:
    """Generates FORAI headers for various programming languages."""
    
    # ...
    
    def update_file_header(self, file_path, header):
        """Update the FORAI header in a file."""
        language = detect_language(file_path)
        
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Check if header already exists
        header_pattern = r'//FORAI:.*?//'
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
                # For JavaScript, insert at the top
                updated_content = header + '\n' + content
                
            else:
                # Default: insert at the top
                updated_content = header + '\n' + content
        
        # Write back to file
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(updated_content)
```

### 5. PHP Parser Implementation

Create a PHP script (`php_parser.php`) using PHP-Parser:

```php
<?php
// php_parser.php
require_once 'vendor/autoload.php';

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

// Parse command line arguments
$file_path = $argv[1];

// Create parser
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

try {
    // Read file content
    $code = file_get_contents($file_path);
    
    // Parse code
    $ast = $parser->parse($code);
    
    // Resolve names
    $nameResolver = new NameResolver();
    $traverser = new NodeTraverser();
    $traverser->addVisitor($nameResolver);
    $ast = $traverser->traverse($ast);
    
    // Extract imports
    $imports = [];
    $definitions = [];
    $exports = [];
    
    // TODO: Traverse AST to extract imports, definitions, and exports
    
    // Output results as JSON
    echo json_encode([
        'imports' => $imports,
        'definitions' => $definitions,
        'exports' => $exports
    ]);
    
} catch (Error $e) {
    echo 'Parse Error: ', $e->getMessage();
    exit(1);
}
```

### 6. JavaScript Parser Implementation

Create a JavaScript script (`js_parser.js`) using Esprima:

```javascript
// js_parser.js
const fs = require('fs');
const esprima = require('esprima');
const path = require('path');

// Parse command line arguments
const filePath = process.argv[2];

try {
    // Read file content
    const code = fs.readFileSync(filePath, 'utf-8');
    
    // Parse code
    const ast = esprima.parseModule(code, { 
        range: true, 
        loc: true, 
        comment: true 
    });
    
    // Extract imports, definitions, and exports
    const imports = [];
    const definitions = [];
    const exports = [];
    
    // TODO: Traverse AST to extract imports, definitions, and exports
    
    // Output results as JSON
    console.log(JSON.stringify({
        imports,
        definitions,
        exports
    }));
    
} catch (error) {
    console.error('Parse Error:', error.message);
    process.exit(1);
}
```

### 7. Update CLI Tools

Update the CLI tools to handle different languages:

```python
def main():
    """Main entry point."""
    # ... (existing argument parsing)
    
    if args.command == 'update':
        # Validate file path
        file_path = os.path.abspath(args.file)
        if not os.path.isfile(file_path):
            logger.error(f"File does not exist: {file_path}")
            return 1
            
        # Detect language
        language = detect_language(file_path)
        
        # Get appropriate analyzer
        if language == 'python':
            analyzer_class = StaticAnalyzer
        elif language == 'php':
            analyzer_class = PHPStaticAnalyzer
        elif language == 'javascript':
            analyzer_class = JavaScriptStaticAnalyzer
        else:
            logger.error(f"Unsupported language: {language}")
            return 1
            
        # Initialize analyzer
        analyzer = analyzer_class(registry)
        
        # Update file header
        imports_changed = update_file_header(file_path, analyzer, registry, args.runtime)
        
        # ...
```

## Implementation Roadmap

1. **Phase 1: Language Detection**
   - Implement language detection module
   - Update CLI tools to detect file types

2. **Phase 2: PHP Support**
   - Implement PHP parser integration
   - Develop PHP-specific AST visitors
   - Test with PHP files

3. **Phase 3: JavaScript Support**
   - Implement JavaScript parser integration
   - Develop JavaScript-specific AST visitors
   - Test with JavaScript files

4. **Phase 4: Header Generation**
   - Update header generator for PHP and JavaScript
   - Implement language-specific header insertion patterns

5. **Phase 5: Testing and Refinement**
   - Test with large PHP and JavaScript codebases
   - Optimize performance for large codebases
   - Refine language-specific features

## Conclusion

Extending FORAI to support PHP and JavaScript requires adding language-specific parsers and adapting the header generator to handle different comment formats. The modular architecture of FORAI makes this extension relatively straightforward, as most components (Symbol Registry, Dependency Tracker, Query API) can remain language-agnostic.