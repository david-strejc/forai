import os
import logging
from typing import Dict, List, Any, Optional

from forai.symbol_registry import SymbolRegistry
from forai.utils.ast_utils import parse_python_file

logger = logging.getLogger(__name__)

class StaticAnalyzer:
    """Static analyzer for Python files.
    
    Analyzes Python files to extract symbols, imports, and exports for FORAI headers.
    """
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the static analyzer.
        
        Args:
            symbol_registry: The symbol registry to use
        """
        self.registry = symbol_registry
        
    def analyze_file(self, file_path: str) -> Dict[str, Any]:
        """Analyze a Python file statically.
        
        Args:
            file_path: Path to the Python file
            
        Returns:
            A dictionary with file_id, definitions, imports, and exports
        """
        logger.info(f"Analyzing file: {file_path}")
        
        # Get file ID
        file_id = self.registry.get_file_id(file_path)
        
        # Parse file
        parse_result = parse_python_file(file_path)
        
        # Convert imports to FORAI format
        imports = self._extract_imports(parse_result['imports'])
        
        # Convert definitions to FORAI format
        definitions = self._extract_definitions(file_id, parse_result['definitions'])
        
        # Get exports
        exports = self._extract_exports(definitions, parse_result['exports'])
        
        return {
            'file_id': file_id,
            'definitions': definitions,
            'imports': imports,
            'exports': exports
        }
    
    def _extract_imports(self, imports: List[Dict[str, Any]]) -> List[Dict[str, str]]:
        """Extract import information from parsed imports.
        
        Args:
            imports: List of parsed imports
            
        Returns:
            List of dictionaries with file_id and symbol_id
        """
        result = []
        
        for imp in imports:
            module = imp['module']
            symbol = imp['symbol']
            
            # Resolve import to file_id and symbol_id
            resolved = self.registry.resolve_import(module, symbol)
            
            if resolved:
                result.append(resolved)
        
        return result
    
    def _extract_definitions(self, file_id: str, definitions: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
        """Extract definition information from parsed definitions.
        
        Args:
            file_id: The file ID
            definitions: List of parsed definitions
            
        Returns:
            List of dictionaries with symbol_id, name, and parents
        """
        result = []
        
        for defn in definitions:
            name = defn['name']
            defn_type = defn['type']
            
            # Get symbol ID
            symbol_id = self.registry.get_symbol_id(file_id, name, defn_type)
            
            # Extract parent classes for classes
            parents = []
            if defn_type == 'class' and 'bases' in defn:
                for base in defn['bases']:
                    # Try to resolve the base class
                    parts = base.split('.')
                    if len(parts) == 1:
                        # Local base class
                        base_symbol = self.registry.registry['files'].get(file_id, {}).get('symbols', {}).get(base)
                        if base_symbol:
                            parents.append(base_symbol)
                    else:
                        # Imported base class, try to resolve
                        module = '.'.join(parts[:-1])
                        base_name = parts[-1]
                        resolved = self.registry.resolve_import(module, base_name)
                        if resolved and resolved.get('symbol_id'):
                            parents.append(resolved['symbol_id'])
            
            result.append({
                'symbol_id': symbol_id,
                'name': name,
                'type': defn_type,
                'parents': parents
            })
        
        return result
    
    def _extract_exports(self, definitions: List[Dict[str, Any]], exports: List[str]) -> List[str]:
        """Extract exports from definitions and parsed exports.
        
        Args:
            definitions: List of processed definitions
            exports: List of exported symbol names
            
        Returns:
            List of exported symbol IDs
        """
        # Build name -> symbol_id map
        name_to_id = {defn['name']: defn['symbol_id'] for defn in definitions}
        
        # Get symbol IDs for exports
        result = []
        for name in exports:
            if name in name_to_id:
                result.append(name_to_id[name])
        
        return result