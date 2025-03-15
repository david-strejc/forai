# FORAI Header VSCode Extension

A VSCode extension that automatically manages FORAI (File Object Reference for AI Interpretation) headers in Python files.

## Features

- Automatically update FORAI headers when saving Python files
- Manually update FORAI headers with commands
- Track dependencies between files and update headers in dependent files
- Optional runtime introspection for more accurate symbol detection

## Commands

- **FORAI: Update Header** - Update the FORAI header in the active Python file
- **FORAI: Update All Headers** - Update FORAI headers in all Python files in the workspace

## Settings

- **forai-header.enableRuntimeIntrospection** - Enable runtime introspection for more accurate symbol detection (default: false)
- **forai-header.updateOnSave** - Automatically update FORAI headers when saving Python files (default: true)

## Requirements

- Python 3.8+
- FORAI Python package (`pip install forai`)

## Installation

1. Install the FORAI Python package:

   ```bash
   pip install forai
   ```

2. Install the extension from the Visual Studio Code Marketplace

## How It Works

The extension integrates with the FORAI Python package to automatically analyze Python files and generate or update FORAI headers. It tracks dependencies between files and updates headers in dependent files when imports change.

FORAI headers look like this:

```python
# //FORAI:F101;DEF[C1:BaseModel,F2:validate_data];IMP[F202:Logger,F303:*];EXP[C1,F2]//
```

These headers provide a compact representation of the file's structure, imports, and exports, which helps AI assistants understand code relationships without parsing entire files.

## Release Notes

### 0.1.0

- Initial release
- Automatic header updates on save
- Manual header update commands
- Dependency tracking
- Optional runtime introspection