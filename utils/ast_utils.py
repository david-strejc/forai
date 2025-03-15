import ast
import logging
from typing import List, Dict, Any, Set, Tuple, Optional

logger = logging.getLogger(__name__)

class ImportVisitor(ast.NodeVisitor):
    """Visitor for extracting import information from an AST."""
    
    def __init__(self):
        self.imports = []
        
    def visit_Import(self, node):
        """Visit an Import node."""
        for name in node.names:
            self.imports.append({
                'module': name.name,
                'symbol': '*',
                'alias': name.asname
            })
        self.generic_visit(node)
        
    def visit_ImportFrom(self, node):
        """Visit an ImportFrom node."""
        if node.module is None:  # relative import with no module
            return
            
        for name in node.names:
            if name.name == '*':
                # Import all symbols
                self.imports.append({
                    'module': node.module,
                    'symbol': '*',
                    'alias': None
                })
            else:
                # Import specific symbol
                self.imports.append({
                    'module': node.module,
                    'symbol': name.name,
                    'alias': name.asname
                })
        self.generic_visit(node)


class DefinitionVisitor(ast.NodeVisitor):
    """Visitor for extracting class and function definitions from an AST."""
    
    def __init__(self):
        self.definitions = []
        self.exports = set()
        
    def visit_ClassDef(self, node):
        """Visit a ClassDef node."""
        # Extract base classes
        bases = []
        for base in node.bases:
            if isinstance(base, ast.Name):
                bases.append(base.id)
            elif isinstance(base, ast.Attribute):
                bases.append(self._get_attribute_name(base))
        
        # Add class definition
        self.definitions.append({
            'name': node.name,
            'type': 'class',
            'bases': bases,
            'docstring': ast.get_docstring(node)
        })
        
        # Add to exports if class is not private
        if not node.name.startswith('_'):
            self.exports.add(node.name)
            
        self.generic_visit(node)
        
    def visit_FunctionDef(self, node):
        """Visit a FunctionDef node."""
        # Add function definition
        self.definitions.append({
            'name': node.name,
            'type': 'function',
            'docstring': ast.get_docstring(node)
        })
        
        # Add to exports if function is not private
        if not node.name.startswith('_'):
            self.exports.add(node.name)
            
        self.generic_visit(node)
        
    def visit_AsyncFunctionDef(self, node):
        """Visit an AsyncFunctionDef node."""
        # Add async function definition
        self.definitions.append({
            'name': node.name,
            'type': 'function',
            'is_async': True,
            'docstring': ast.get_docstring(node)
        })
        
        # Add to exports if function is not private
        if not node.name.startswith('_'):
            self.exports.add(node.name)
            
        self.generic_visit(node)
        
    def visit_Assign(self, node):
        """Visit an assignment to catch module-level variables."""
        # Only consider module-level assignments
        if not hasattr(node, 'parent') or not isinstance(node.parent, ast.Module):
            return
            
        # Extract targets
        for target in node.targets:
            if isinstance(target, ast.Name):
                # Skip private variables
                if target.id.startswith('_') and target.id != '__all__':
                    continue
                    
                # Handle __all__ special case
                if target.id == '__all__' and isinstance(node.value, ast.List):
                    for elt in node.value.elts:
                        if isinstance(elt, ast.Str):
                            self.exports.add(elt.s)
                    return
                
                # Add variable definition
                self.definitions.append({
                    'name': target.id,
                    'type': 'variable'
                })
                
                # Add to exports
                if not target.id.startswith('_'):
                    self.exports.add(target.id)
                    
        self.generic_visit(node)
        
    def _get_attribute_name(self, node):
        """Recursively build an attribute name."""
        if isinstance(node, ast.Name):
            return node.id
        elif isinstance(node, ast.Attribute):
            return f"{self._get_attribute_name(node.value)}.{node.attr}"
        return "unknown"


def parse_python_file(file_path: str) -> Dict[str, Any]:
    """Parse a Python file and extract imports, definitions, and exports.
    
    Args:
        file_path: Path to the Python file
        
    Returns:
        A dictionary with imports, definitions, and exports
    """
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception as e:
        logger.error(f"Failed to read file {file_path}: {e}")
        return {'imports': [], 'definitions': [], 'exports': []}
    
    try:
        # Parse file
        tree = ast.parse(content, filename=file_path)
        
        # Add parent reference to each node
        for node in ast.walk(tree):
            for child in ast.iter_child_nodes(node):
                child.parent = node
        
        # Extract imports
        import_visitor = ImportVisitor()
        import_visitor.visit(tree)
        
        # Extract definitions and exports
        definition_visitor = DefinitionVisitor()
        definition_visitor.visit(tree)
        
        return {
            'imports': import_visitor.imports,
            'definitions': definition_visitor.definitions,
            'exports': list(definition_visitor.exports)
        }
    except SyntaxError as e:
        logger.error(f"Syntax error in file {file_path}: {e}")
        return {'imports': [], 'definitions': [], 'exports': []}
    except Exception as e:
        logger.error(f"Failed to parse file {file_path}: {e}")
        return {'imports': [], 'definitions': [], 'exports': []}