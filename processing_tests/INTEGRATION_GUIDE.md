# FORAI Integration Guide for Multi-Language Support

This guide demonstrates how to fully integrate PHP and JavaScript support into FORAI.

## 1. File Structure

```
forai/
├── __init__.py
├── cli.py                           # Updated to support multiple languages
├── query.py                         # Updated to support multiple languages
├── language_detection/              # NEW: Language detection module
│   ├── __init__.py
│   └── detector.py
├── static_analyzer/                 # Updated for language-specific analyzers
│   ├── __init__.py
│   ├── analyzer.py                  # Main analyzer with language detection
│   ├── python_analyzer.py           # Python-specific analyzer
│   ├── php_analyzer.py              # NEW: PHP-specific analyzer
│   └── js_analyzer.py               # NEW: JavaScript-specific analyzer
├── runtime_introspector/
│   ├── __init__.py
│   └── introspector.py              # Updated for language-specific introspection
├── header_generator/
│   ├── __init__.py
│   └── generator.py                 # Updated for language-specific headers
├── dependency_tracker/
│   ├── __init__.py
│   └── tracker.py                   # Language-agnostic
├── symbol_registry/
│   ├── __init__.py
│   └── registry.py                  # Language-agnostic
├── external_parsers/                # NEW: External parsers for PHP and JS
│   ├── php_parser.php
│   └── js_parser.js
└── utils/
    ├── __init__.py
    ├── php_utils.py                 # NEW: PHP-specific utilities
    ├── js_utils.py                  # NEW: JavaScript-specific utilities
    └── ast_utils.py                 # Updated for language-specific AST handling
```

## 2. Implementation Steps

### 2.1 Language Detection Module

Create the language detection module to automatically detect file languages:

```python
# forai/language_detection/detector.py
import os
import re

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
```

### 2.2 Update Static Analyzer

Modify the main static analyzer to use language-specific analyzers:

```python
# forai/static_analyzer/analyzer.py
import os
import logging
from typing import Dict, Any

from forai.language_detection.detector import detect_language
from forai.static_analyzer.python_analyzer import PythonStaticAnalyzer
from forai.static_analyzer.php_analyzer import PHPStaticAnalyzer
from forai.static_analyzer.js_analyzer import JavaScriptStaticAnalyzer
from forai.symbol_registry import SymbolRegistry

logger = logging.getLogger(__name__)

class StaticAnalyzer:
    """Static analyzer for various programming languages."""
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the static analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        self.python_analyzer = PythonStaticAnalyzer(symbol_registry)
        self.php_analyzer = PHPStaticAnalyzer(symbol_registry)
        self.js_analyzer = JavaScriptStaticAnalyzer(symbol_registry)
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a file statically.
        
        Args:
            file_path: Path to the file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        logger.info(f"Analyzing file: {file_path}")
        
        # Detect language
        language = detect_language(file_path)
        logger.info(f"Detected language: {language}")
        
        # Select appropriate analyzer
        if language == 'python':
            result = self.python_analyzer.analyze_file(file_path)
        elif language == 'php':
            result = self.php_analyzer.analyze_file(file_path)
        elif language == 'javascript':
            result = self.js_analyzer.analyze_file(file_path)
        else:
            logger.warning(f"Unsupported language: {language}")
            result = {
                'file_id': self.registry.get_file_id(file_path),
                'definitions': [],
                'imports': [],
                'exports': []
            }
        
        # Add language information
        result['language'] = language
        
        return result
```

### 2.3 Update Header Generator

Update the header generator to handle language-specific header insertion patterns:

```python
# forai/header_generator/generator.py
import re
import logging
from typing import Dict, Any

from forai.symbol_registry import SymbolRegistry
from forai.language_detection.detector import detect_language

logger = logging.getLogger(__name__)

class HeaderGenerator:
    """Generates FORAI headers for various programming languages."""
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the header generator.
        
        Args:
            symbol_registry: The symbol registry
        """
        self.registry = symbol_registry
        
    def generate_header(self, file_data: Dict[str, Any]) -> str:
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
    
    def update_file_header(self, file_path: str, header: str) -> None:
        """Update the FORAI header in a file.
        
        Args:
            file_path: Path to the file
            header: The FORAI header to add or update
        """
        # Detect language
        language = detect_language(file_path)
        
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
        except Exception as e:
            logger.error(f"Failed to read file {file_path}: {e}")
            return
        
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
                    
                # Skip any initial empty lines
                while len(lines) > insert_pos and not lines[insert_pos].strip():
                    insert_pos += 1
                    
                # Insert header
                lines.insert(insert_pos, header)
                
                # Add blank line after header if there isn't one already
                if len(lines) > insert_pos + 1 and lines[insert_pos + 1].strip():
                    lines.insert(insert_pos + 1, '')
                    
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
                # For JavaScript, insert at the top, but after any initial comment block
                comment_end = 0
                if content.startswith('/*'):
                    end_match = re.search(r'\*/', content)
                    if end_match:
                        comment_end = end_match.end()
                elif content.startswith('//'):
                    lines = content.splitlines()
                    for i, line in enumerate(lines):
                        if not line.strip().startswith('//'):
                            comment_end = sum(len(line) + 1 for line in lines[:i])
                            break
                
                if comment_end > 0:
                    updated_content = content[:comment_end] + '\n' + header + '\n' + content[comment_end:]
                else:
                    updated_content = header + '\n' + content
                    
            else:
                # Default: insert at the top
                updated_content = header + '\n' + content
        
        # Write back to file
        try:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(updated_content)
        except Exception as e:
            logger.error(f"Failed to write file {file_path}: {e}")
```

### 2.4 PHP Analyzer Implementation

Create the PHP analyzer that uses an external PHP parser:

```python
# forai/static_analyzer/php_analyzer.py
import os
import subprocess
import json
import logging
import tempfile
import re
from typing import Dict, List, Any

from forai.symbol_registry import SymbolRegistry

logger = logging.getLogger(__name__)

class PHPStaticAnalyzer:
    """Static analyzer for PHP files."""
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the PHP analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        self.parser_path = os.path.join(os.path.dirname(os.path.dirname(__file__)), 
                                       'external_parsers', 'php_parser.php')
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a PHP file statically.
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        logger.info(f"Analyzing PHP file: {file_path}")
        
        # Check if PHP is available
        try:
            subprocess.run(['php', '--version'], capture_output=True, check=True)
        except (subprocess.SubprocessError, FileNotFoundError):
            logger.warning("PHP not available, falling back to regex-based analysis")
            return self._analyze_with_regex(file_path)
        
        # Check if PHP parser exists
        if not os.path.exists(self.parser_path):
            logger.warning(f"PHP parser not found: {self.parser_path}")
            return self._analyze_with_regex(file_path)
            
        try:
            # Run PHP parser
            result = subprocess.run(['php', self.parser_path, file_path], 
                                   capture_output=True, text=True)
            
            if result.returncode != 0:
                logger.warning(f"PHP parser failed: {result.stderr}")
                return self._analyze_with_regex(file_path)
                
            # Parse JSON output
            output = json.loads(result.stdout)
            
            # Get file ID
            file_id = self.registry.get_file_id(file_path)
            
            # Extract definitions
            definitions = []
            for defn in output.get('definitions', []):
                defn_type = defn.get('type')
                name = defn.get('name')
                
                if not name:
                    continue
                    
                # Get symbol ID
                symbol_id = self.registry.get_symbol_id(file_id, name, defn_type)
                
                # Build definition
                definition = {
                    'symbol_id': symbol_id,
                    'name': name,
                    'type': defn_type
                }
                
                # Add parent classes if class and has parents
                if defn_type == 'class' and 'extends' in defn:
                    parent = defn['extends']
                    definition['parents'] = [parent]
                
                definitions.append(definition)
            
            # Extract imports
            imports = []
            for imp in output.get('imports', []):
                file_ref = imp.get('file')
                symbol = imp.get('symbol', '*')
                
                # Resolve import to file_id and symbol_id
                resolved = self.registry.resolve_import(file_ref, symbol)
                
                if resolved:
                    imports.append(resolved)
                else:
                    imports.append({
                        'file_id': 'unknown',
                        'symbol_id': symbol
                    })
            
            # Determine exports
            exports = [def_item['symbol_id'] for def_item in definitions
                      if not def_item['name'].startswith('_')]
            
            return {
                'file_id': file_id,
                'definitions': definitions,
                'imports': imports,
                'exports': exports
            }
        except Exception as e:
            logger.error(f"Error analyzing PHP file {file_path}: {e}")
            return self._analyze_with_regex(file_path)
    
    def _analyze_with_regex(self, file_path: str) -> Dict[str, Any]:
        """Analyze a PHP file with regex (fallback method).
        
        Args:
            file_path: Path to the PHP file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Extract basic information using regex
        
        # Find namespace
        namespace = None
        namespace_match = re.search(r'namespace\s+([^;]+)', content)
        if namespace_match:
            namespace = namespace_match.group(1).strip()
        
        # Find class definitions
        classes = []
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, class_name, 'class')
            
            # Add class definition
            class_def = {
                'symbol_id': symbol_id,
                'name': class_name,
                'type': 'class'
            }
            
            # Add parent class if exists
            if parent_class:
                class_def['parents'] = [parent_class]
            
            classes.append(class_def)
        
        # Find function definitions
        functions = []
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, function_name, 'function')
            
            # Add function definition
            functions.append({
                'symbol_id': symbol_id,
                'name': function_name,
                'type': 'function'
            })
        
        # Find imports
        imports = []
        import_matches = re.finditer(r'(require|include|require_once|include_once)\s*\(?\s*[\'"]([^\'"]+)[\'"]', content)
        for match in import_matches:
            import_type = match.group(1)
            import_path = match.group(2)
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': '*'
            })
        
        # Find use statements (imports)
        use_matches = re.finditer(r'use\s+([^;]+);', content)
        for match in use_matches:
            use_path = match.group(1).strip()
            
            # Handle aliased imports: use Some\Namespace\Class as Alias
            if ' as ' in use_path:
                parts = use_path.split(' as ')
                use_path = parts[0].strip()
                alias = parts[1].strip()
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': use_path.split('\\')[-1]
            })
        
        # Combine definitions
        definitions = classes + functions
        
        # Determine exports
        exports = [def_item['symbol_id'] for def_item in definitions 
                  if not def_item['name'].startswith('_')]
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
```

### 2.5 JavaScript Analyzer Implementation

Create the JavaScript analyzer that uses an external JavaScript parser:

```python
# forai/static_analyzer/js_analyzer.py
import os
import subprocess
import json
import logging
import tempfile
import re
from typing import Dict, List, Any

from forai.symbol_registry import SymbolRegistry

logger = logging.getLogger(__name__)

class JavaScriptStaticAnalyzer:
    """Static analyzer for JavaScript files."""
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the JavaScript analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        self.parser_path = os.path.join(os.path.dirname(os.path.dirname(__file__)),
                                      'external_parsers', 'js_parser.js')
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a JavaScript file statically.
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        logger.info(f"Analyzing JavaScript file: {file_path}")
        
        # Check if Node.js is available
        try:
            subprocess.run(['node', '--version'], capture_output=True, check=True)
        except (subprocess.SubprocessError, FileNotFoundError):
            logger.warning("Node.js not available, falling back to regex-based analysis")
            return self._analyze_with_regex(file_path)
        
        # Check if JavaScript parser exists
        if not os.path.exists(self.parser_path):
            logger.warning(f"JavaScript parser not found: {self.parser_path}")
            return self._analyze_with_regex(file_path)
            
        try:
            # Run JavaScript parser
            result = subprocess.run(['node', self.parser_path, file_path],
                                   capture_output=True, text=True)
            
            if result.returncode != 0:
                logger.warning(f"JavaScript parser failed: {result.stderr}")
                return self._analyze_with_regex(file_path)
                
            # Parse JSON output
            output = json.loads(result.stdout)
            
            # Get file ID
            file_id = self.registry.get_file_id(file_path)
            
            # Extract definitions
            definitions = []
            for defn in output.get('definitions', []):
                defn_type = defn.get('type')
                name = defn.get('name')
                
                if not name:
                    continue
                    
                # Get symbol ID
                symbol_id = self.registry.get_symbol_id(file_id, name, defn_type)
                
                # Build definition
                definition = {
                    'symbol_id': symbol_id,
                    'name': name,
                    'type': defn_type
                }
                
                # Add parent classes if class and has parents
                if defn_type == 'class' and 'extends' in defn:
                    parent = defn['extends']
                    definition['parents'] = [parent]
                
                definitions.append(definition)
            
            # Extract imports
            imports = []
            for imp in output.get('imports', []):
                module = imp.get('module')
                symbol = imp.get('symbol', '*')
                
                # For JavaScript, imports are often relative paths or packages
                imports.append({
                    'file_id': 'unknown',
                    'symbol_id': symbol
                })
            
            # Determine exports
            exports = []
            for exp in output.get('exports', []):
                name = exp.get('name')
                for def_item in definitions:
                    if def_item['name'] == name:
                        exports.append(def_item['symbol_id'])
                        break
            
            # If no exports found but 'default' is set, assume all definitions are exported
            if not exports and output.get('default', False):
                exports = [def_item['symbol_id'] for def_item in definitions]
            
            return {
                'file_id': file_id,
                'definitions': definitions,
                'imports': imports,
                'exports': exports
            }
        except Exception as e:
            logger.error(f"Error analyzing JavaScript file {file_path}: {e}")
            return self._analyze_with_regex(file_path)
    
    def _analyze_with_regex(self, file_path: str) -> Dict[str, Any]:
        """Analyze a JavaScript file with regex (fallback method).
        
        Args:
            file_path: Path to the JavaScript file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Read file content
        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Extract basic information using regex
        
        # Find class definitions (ES6 classes)
        classes = []
        class_matches = re.finditer(r'class\s+(\w+)(?:\s+extends\s+(\w+))?', content)
        for match in class_matches:
            class_name = match.group(1)
            parent_class = match.group(2)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, class_name, 'class')
            
            # Add class definition
            class_def = {
                'symbol_id': symbol_id,
                'name': class_name,
                'type': 'class'
            }
            
            # Add parent class if exists
            if parent_class:
                class_def['parents'] = [parent_class]
            
            classes.append(class_def)
        
        # Find function definitions
        functions = []
        
        # Regular functions
        function_matches = re.finditer(r'function\s+(\w+)\s*\(', content)
        for match in function_matches:
            function_name = match.group(1)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, function_name, 'function')
            
            # Add function definition
            functions.append({
                'symbol_id': symbol_id,
                'name': function_name,
                'type': 'function'
            })
        
        # Methods in object literals
        method_matches = re.finditer(r'(\w+)\s*:\s*function\s*\(', content)
        for match in method_matches:
            method_name = match.group(1)
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, method_name, 'function')
            
            # Add function definition
            functions.append({
                'symbol_id': symbol_id,
                'name': method_name,
                'type': 'function'
            })
        
        # Find import statements (ES6)
        imports = []
        
        # import { symbol } from 'module'
        import_matches = re.finditer(r'import\s+\{([^}]+)\}\s+from\s+[\'"]([^\'"]+)[\'"]', content)
        for match in import_matches:
            imported_symbols = [s.strip() for s in match.group(1).split(',')]
            module_name = match.group(2)
            
            for symbol in imported_symbols:
                imports.append({
                    'file_id': 'unknown',
                    'symbol_id': symbol
                })
        
        # import module from 'module'
        import_default_matches = re.finditer(r'import\s+(\w+)\s+from\s+[\'"]([^\'"]+)[\'"]', content)
        for match in import_default_matches:
            imported_symbol = match.group(1)
            module_name = match.group(2)
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': imported_symbol
            })
        
        # import * as name from 'module'
        import_all_matches = re.finditer(r'import\s+\*\s+as\s+(\w+)\s+from\s+[\'"]([^\'"]+)[\'"]', content)
        for match in import_all_matches:
            namespace = match.group(1)
            module_name = match.group(2)
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': '*'
            })
        
        # Find require statements (CommonJS)
        require_matches = re.finditer(r'(?:const|let|var)\s+(\w+)\s*=\s*require\([\'"]([^\'"]+)[\'"]\)', content)
        for match in require_matches:
            variable_name = match.group(1)
            module_name = match.group(2)
            
            imports.append({
                'file_id': 'unknown',
                'symbol_id': '*'
            })
        
        # Find export statements (ES6)
        exports = []
        
        # Named exports
        export_matches = re.finditer(r'export\s+(const|let|var|function|class)\s+(\w+)', content)
        for match in export_matches:
            export_type = match.group(1)
            export_name = match.group(2)
            
            # Find symbol ID
            for def_item in classes + functions:
                if def_item['name'] == export_name:
                    exports.append(def_item['symbol_id'])
                    break
        
        # Export default
        export_default_matches = re.finditer(r'export\s+default\s+(\w+)', content)
        for match in export_default_matches:
            export_name = match.group(1)
            
            for def_item in classes + functions:
                if def_item['name'] == export_name:
                    exports.append(def_item['symbol_id'])
                    break
        
        # If no exports found, assume all definitions are exported
        if not exports:
            exports = [def_item['symbol_id'] for def_item in classes + functions]
        
        # Combine definitions
        definitions = classes + functions
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
```

### 2.6 External Parsers

Create external parsers for PHP and JavaScript to perform accurate analysis:

#### PHP Parser (external_parsers/php_parser.php)

```php
<?php
// Use PHP's built-in tokenizer for basic analysis
// In a real implementation, you'd use PHP-Parser or similar

if ($argc < 2) {
    die("Usage: php php_parser.php <file_path>\n");
}

$file_path = $argv[1];
$content = file_get_contents($file_path);

if ($content === false) {
    die(json_encode(['error' => 'Failed to read file']));
}

$definitions = [];
$imports = [];
$namespace = null;

// Get namespace
if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
    $namespace = trim($matches[1]);
}

// Get class definitions
preg_match_all('/class\s+(\w+)(?:\s+extends\s+(\w+))?/', $content, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $class_def = [
        'type' => 'class',
        'name' => $match[1]
    ];
    
    if (isset($match[2])) {
        $class_def['extends'] = $match[2];
    }
    
    $definitions[] = $class_def;
}

// Get function definitions
preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $definitions[] = [
        'type' => 'function',
        'name' => $match[1]
    ];
}

// Get require/include statements
preg_match_all('/(require|include|require_once|include_once)\s*\(?\s*[\'"]([^\'"]+)[\'"]/', $content, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $imports[] = [
        'type' => $match[1],
        'file' => $match[2],
        'symbol' => '*'
    ];
}

// Get use statements
preg_match_all('/use\s+([^;]+);/', $content, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $use_path = trim($match[1]);
    
    // Handle aliased imports: use Some\Namespace\Class as Alias
    $alias = null;
    if (strpos($use_path, ' as ') !== false) {
        list($use_path, $alias) = array_map('trim', explode(' as ', $use_path));
    }
    
    $parts = explode('\\', $use_path);
    $symbol = end($parts);
    
    $imports[] = [
        'type' => 'use',
        'namespace' => $use_path,
        'symbol' => $symbol,
        'alias' => $alias
    ];
}

// Output result
echo json_encode([
    'namespace' => $namespace,
    'definitions' => $definitions,
    'imports' => $imports
]);
```

#### JavaScript Parser (external_parsers/js_parser.js)

```javascript
// JavaScript parser using Esprima
// In a real implementation, this would require the Esprima package

const fs = require('fs');
const path = require('path');

if (process.argv.length < 3) {
    console.error("Usage: node js_parser.js <file_path>");
    process.exit(1);
}

const filePath = process.argv[2];

try {
    const content = fs.readFileSync(filePath, 'utf-8');
    
    // Without Esprima, use regex for basic analysis
    const definitions = [];
    const imports = [];
    let hasDefault = false;
    
    // Find class definitions
    const classPattern = /class\s+(\w+)(?:\s+extends\s+(\w+))?/g;
    let match;
    while ((match = classPattern.exec(content)) !== null) {
        const def = {
            type: 'class',
            name: match[1]
        };
        
        if (match[2]) {
            def.extends = match[2];
        }
        
        definitions.push(def);
    }
    
    // Find function definitions
    const funcPattern = /function\s+(\w+)\s*\(/g;
    while ((match = funcPattern.exec(content)) !== null) {
        definitions.push({
            type: 'function',
            name: match[1]
        });
    }
    
    // Find method definitions in objects
    const methodPattern = /(\w+)\s*:\s*function\s*\(/g;
    while ((match = methodPattern.exec(content)) !== null) {
        definitions.push({
            type: 'function',
            name: match[1]
        });
    }
    
    // Find imports - ES6 named imports
    const importPattern = /import\s+\{([^}]+)\}\s+from\s+['"](.*?)['"]/g;
    while ((match = importPattern.exec(content)) !== null) {
        const symbols = match[1].split(',').map(s => s.trim());
        const module = match[2];
        
        for (const symbol of symbols) {
            imports.push({
                type: 'import',
                module: module,
                symbol: symbol
            });
        }
    }
    
    // Find imports - ES6 default imports
    const defaultImportPattern = /import\s+(\w+)\s+from\s+['"](.*?)['"]/g;
    while ((match = defaultImportPattern.exec(content)) !== null) {
        imports.push({
            type: 'import',
            module: match[2],
            symbol: match[1],
            default: true
        });
    }
    
    // Find imports - CommonJS require
    const requirePattern = /(?:const|let|var)\s+(\w+)\s*=\s*require\(['"](.*?)['"]\)/g;
    while ((match = requirePattern.exec(content)) !== null) {
        imports.push({
            type: 'require',
            module: match[2],
            symbol: match[1]
        });
    }
    
    // Find exports - Named exports
    const exportPattern = /export\s+(const|let|var|function|class)\s+(\w+)/g;
    const exports = [];
    while ((match = exportPattern.exec(content)) !== null) {
        exports.push({
            type: match[1],
            name: match[2]
        });
    }
    
    // Find exports - Default export
    const defaultExportPattern = /export\s+default\s+(\w+)/g;
    while ((match = defaultExportPattern.exec(content)) !== null) {
        exports.push({
            name: match[1],
            default: true
        });
        hasDefault = true;
    }
    
    console.log(JSON.stringify({
        definitions,
        imports,
        exports,
        default: hasDefault
    }));
} catch (error) {
    console.error(`Error: ${error.message}`);
    process.exit(1);
}
```

## 3. Update CLI Tools

Update the CLI tools to handle different languages:

```python
# forai/cli.py
# ...

def update_file_header(file_path: str, registry: SymbolRegistry, enable_runtime: bool) -> bool:
    """Update the FORAI header in a file."""
    # Initialize components
    analyzer = StaticAnalyzer(registry)
    header_generator = HeaderGenerator(registry)
    
    # Get previous header imports
    previous_imports = set()
    try:
        dependency_tracker = DependencyTracker(registry)
        previous_imports = set(dependency_tracker._get_header_imports(file_path))
    except Exception as e:
        logger.debug(f"Failed to get previous imports: {e}")
    
    # Analyze file
    file_data = analyzer.analyze_file(file_path)
    
    # Add runtime information if requested and it's a Python file
    if enable_runtime and file_data.get('language') == 'python':
        runtime_introspector = RuntimeIntrospector()
        runtime_data = runtime_introspector.introspect(file_path)
        file_data = merge_static_and_runtime(file_data, runtime_data)
    
    # Generate header
    header = header_generator.generate_header(file_data)
    
    # Update file
    header_generator.update_file_header(file_path, header)
    
    # Check if imports changed
    current_imports = set()
    for imp in file_data.get('imports', []):
        file_id = imp.get('file_id', '')
        symbol_id = imp.get('symbol_id', '*')
        if file_id:
            current_imports.add(f"{file_id}:{symbol_id}")
    
    return previous_imports != current_imports

# ...
```

## 4. Update Query API

Update the query API to handle different languages:

```python
# forai/query.py
# ...

def get_file_header(self, file_path: str) -> Optional[str]:
    """Get the FORAI header from a file."""
    if not os.path.exists(file_path):
        return None
        
    with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read(2000)  # Read first 2000 characters to find header
    
    # Look for header
    header_match = re.search(r'//FORAI:(.*?)//', content)
    if not header_match:
        return None
        
    header = header_match.group(0)
    
    # Parse language information
    language = 'unknown'
    lang_match = re.search(r'LANG\[([^\]]+)\]', header)
    if lang_match:
        language = lang_match.group(1)
    
    return {
        'header': header,
        'language': language
    }

# ...
```

## 5. Implementation Process

To implement this multi-language support:

1. **Create directory structure** - Set up the new directories and files.
2. **Implement language detection** - Create the language detection module.
3. **Implement PHP analyzer** - Create the PHP analyzer with regex fallback.
4. **Implement JavaScript analyzer** - Create the JavaScript analyzer with regex fallback.
5. **Update header generator** - Add language-specific header insertion logic.
6. **Create external parsers** - Create PHP and JavaScript parsers for more accurate analysis.
7. **Update main components** - Update static analyzer and CLI tools to handle multiple languages.
8. **Test with sample files** - Test the implementation with PHP and JavaScript files.
9. **Refine and optimize** - Refine the implementation based on test results.

## 6. Testing

To test the multi-language support:

```bash
# Install forai
pip install -e .

# Test with PHP file
forai --workspace /path/to/workspace update path/to/file.php

# Test with JavaScript file
forai --workspace /path/to/workspace update path/to/file.js

# Test with Python file
forai --workspace /path/to/workspace update path/to/file.py

# Update all files in a workspace
forai --workspace /path/to/workspace update-all

# Query headers
forai-query --workspace /path/to/workspace header path/to/file.php
forai-query --workspace /path/to/workspace header path/to/file.js
```

By implementing these changes, FORAI will be able to handle PHP and JavaScript files in addition to Python files, providing a comprehensive solution for managing code references in multilingual projects.