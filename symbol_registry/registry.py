import json
import os
import logging
from typing import Dict, Optional, Any

logger = logging.getLogger(__name__)

class SymbolRegistry:
    """Manages the symbol registry for the FORAI system.
    
    The registry keeps track of all files, their unique IDs, and the symbols defined in each file.
    """
    
    def __init__(self, workspace_path: str):
        """Initialize the symbol registry.
        
        Args:
            workspace_path: Path to the workspace root directory
        """
        self.workspace_path = workspace_path
        self.registry_path = os.path.join(workspace_path, '.forai', 'registry.json')
        self.registry = self._load_registry()
        
    def _load_registry(self) -> Dict[str, Any]:
        """Load the symbol registry from disk."""
        if os.path.exists(self.registry_path):
            try:
                with open(self.registry_path, 'r') as f:
                    return json.load(f)
            except json.JSONDecodeError:
                logger.warning(f"Failed to parse registry file {self.registry_path}, creating new registry")
        
        # Create new registry
        os.makedirs(os.path.dirname(self.registry_path), exist_ok=True)
        registry = {
            'files': {},
            'next_file_id': 101,
            'file_paths': {}
        }
        self._save_registry(registry)
        return registry
    
    def _save_registry(self, registry: Optional[Dict[str, Any]] = None) -> None:
        """Save the symbol registry to disk."""
        if registry is None:
            registry = self.registry
        
        os.makedirs(os.path.dirname(self.registry_path), exist_ok=True)
        with open(self.registry_path, 'w') as f:
            json.dump(registry, f, indent=2)
    
    def get_file_id(self, file_path: str) -> str:
        """Get or create a file ID for the given path.
        
        Args:
            file_path: Absolute path to the file
            
        Returns:
            The file ID (e.g., "F101")
        """
        rel_path = os.path.relpath(file_path, self.workspace_path)
        
        # Check if path exists in registry
        if rel_path in self.registry['file_paths']:
            return self.registry['file_paths'][rel_path]
        
        # Create new file ID
        file_id = f"F{self.registry['next_file_id']}"
        self.registry['next_file_id'] += 1
        self.registry['file_paths'][rel_path] = file_id
        self.registry['files'][file_id] = {
            'path': rel_path,
            'symbols': {},
            'next_class_id': 1,
            'next_func_id': 1
        }
        self._save_registry()
        return file_id
    
    def get_symbol_id(self, file_id: str, symbol_name: str, symbol_type: str) -> str:
        """Get or create a symbol ID for the given name and type.
        
        Args:
            file_id: The file ID (e.g., "F101")
            symbol_name: The name of the symbol (e.g., "UserModel")
            symbol_type: The type of the symbol ("class" or "function")
            
        Returns:
            The symbol ID (e.g., "C1" for a class, "F2" for a function)
        """
        file_entry = self.registry['files'].get(file_id, {
            'symbols': {},
            'next_class_id': 1,
            'next_func_id': 1
        })
        
        # Check if symbol exists
        if symbol_name in file_entry['symbols']:
            return file_entry['symbols'][symbol_name]
        
        # Create new symbol ID
        if symbol_type == 'class':
            symbol_id = f"C{file_entry['next_class_id']}"
            file_entry['next_class_id'] += 1
        else:  # function or variable
            symbol_id = f"F{file_entry['next_func_id']}"
            file_entry['next_func_id'] += 1
            
        file_entry['symbols'][symbol_name] = symbol_id
        self.registry['files'][file_id] = file_entry
        self._save_registry()
        return symbol_id
    
    def resolve_import(self, module_name: str, symbol_name: str) -> Optional[Dict[str, str]]:
        """Resolve an import to a file_id:symbol_id reference.
        
        Args:
            module_name: The name of the imported module
            symbol_name: The name of the imported symbol
            
        Returns:
            A dictionary with file_id and symbol_id, or None if not found
        """
        # First try to find the module path in the registry
        for file_id, file_info in self.registry['files'].items():
            file_path = os.path.join(self.workspace_path, file_info['path'])
            module_path = os.path.splitext(file_path)[0]
            
            if module_path.endswith(module_name.replace('.', '/')):
                # If symbol is "*", return the file_id only
                if symbol_name == "*":
                    return {"file_id": file_id, "symbol_id": "*"}
                
                # Check if the symbol exists in this file
                for sym_name, sym_id in file_info.get('symbols', {}).items():
                    if sym_name == symbol_name:
                        return {"file_id": file_id, "symbol_id": sym_id}
                
                # Symbol not found in this file
                return {"file_id": file_id, "symbol_id": None}
        
        # Module not found
        return None
    
    def update_file_path(self, old_path: str, new_path: str) -> str:
        """Update a file path in the registry.
        
        Args:
            old_path: The old file path
            new_path: The new file path
            
        Returns:
            The file ID
        """
        rel_old_path = os.path.relpath(old_path, self.workspace_path)
        rel_new_path = os.path.relpath(new_path, self.workspace_path)
        
        if rel_old_path in self.registry['file_paths']:
            file_id = self.registry['file_paths'][rel_old_path]
            del self.registry['file_paths'][rel_old_path]
            self.registry['file_paths'][rel_new_path] = file_id
            
            if file_id in self.registry['files']:
                self.registry['files'][file_id]['path'] = rel_new_path
                
            self._save_registry()
            return file_id
        
        # Old path not found, treat as new file
        return self.get_file_id(new_path)
    
    def remove_file(self, file_id: str) -> None:
        """Remove a file from the registry.
        
        Args:
            file_id: The file ID to remove
        """
        if file_id in self.registry['files']:
            file_info = self.registry['files'][file_id]
            rel_path = file_info['path']
            
            if rel_path in self.registry['file_paths']:
                del self.registry['file_paths'][rel_path]
                
            del self.registry['files'][file_id]
            self._save_registry()