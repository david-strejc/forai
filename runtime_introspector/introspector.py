import importlib.util
import inspect
import sys
import os
import logging
import types
from typing import Dict, Any, List, Set, Optional

logger = logging.getLogger(__name__)

class RuntimeIntrospector:
    """Runtime introspector for Python files.
    
    Performs runtime introspection on Python modules to extract additional information
    that might not be available through static analysis.
    """
    
    def __init__(self):
        """Initialize the runtime introspector."""
        pass
        
    def introspect(self, file_path: str) -> Dict[str, Any]:
        """Perform runtime introspection on a module.
        
        Args:
            file_path: Path to the Python file
            
        Returns:
            A dictionary with runtime_symbols and imported_modules
        """
        logger.info(f"Introspecting file: {file_path}")
        
        try:
            # Generate a module name from the file path
            module_name = os.path.splitext(os.path.basename(file_path))[0]
            
            # Create a spec for the module
            spec = importlib.util.spec_from_file_location(module_name, file_path)
            if spec is None or spec.loader is None:
                raise ImportError(f"Could not load module from {file_path}")
                
            # Create the module
            module = importlib.util.module_from_spec(spec)
            
            # Track imported modules
            original_import = __import__
            imported_modules = {}
            
            def custom_import(name, *args, **kwargs):
                imported_modules[name] = True
                return original_import(name, *args, **kwargs)
            
            # Replace builtin import
            sys.modules[module_name] = module
            sys.__import__ = custom_import
            
            try:
                # Execute the module
                spec.loader.exec_module(module)
            finally:
                # Restore original import
                sys.__import__ = original_import
                
                # Clean up
                if module_name in sys.modules:
                    del sys.modules[module_name]
            
            # Introspect the module
            runtime_symbols = {}
            for name, obj in inspect.getmembers(module):
                if name.startswith('_'):  # Skip private symbols
                    continue
                    
                if inspect.isclass(obj):
                    bases = []
                    for base in obj.__bases__:
                        if base is not object:
                            bases.append(base.__name__)
                            
                    runtime_symbols[name] = {
                        'type': 'class',
                        'bases': bases,
                        'docstring': inspect.getdoc(obj)
                    }
                elif inspect.isfunction(obj):
                    runtime_symbols[name] = {
                        'type': 'function',
                        'signature': str(inspect.signature(obj)),
                        'docstring': inspect.getdoc(obj)
                    }
                elif not inspect.ismodule(obj) and not inspect.isbuiltin(obj):
                    # Consider other objects as variables
                    runtime_symbols[name] = {
                        'type': 'variable',
                        'value_type': type(obj).__name__
                    }
            
            return {
                'runtime_symbols': runtime_symbols,
                'imported_modules': list(imported_modules.keys())
            }
            
        except Exception as e:
            logger.error(f"Failed to introspect {file_path}: {e}")
            return {
                'error': str(e),
                'runtime_symbols': {},
                'imported_modules': []
            }