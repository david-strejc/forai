import re
import logging
from typing import Dict, List, Any

from forai.symbol_registry import SymbolRegistry

logger = logging.getLogger(__name__)

class HeaderGenerator:
    """Generates FORAI headers for Python files."""
    
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
            header += f"EXP[{','.join(exp_parts)}]//"
        else:
            header += "EXP[]//"`
        
        return header
    
    def update_file_header(self, file_path: str, header: str) -> None:
        """Update the FORAI header in a file.
        
        Args:
            file_path: Path to the file
            header: The FORAI header to add or update
        """
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
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
            # Add header at the top, preserving any shebang line or encoding declaration
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
        
        # Write back to file
        try:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(updated_content)
        except Exception as e:
            logger.error(f"Failed to write file {file_path}: {e}")
            return