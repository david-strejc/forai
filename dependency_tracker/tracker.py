import os
import re
import logging
from typing import Dict, List, Set, Any, Optional

from forai.symbol_registry import SymbolRegistry
from forai.static_analyzer import StaticAnalyzer
from forai.header_generator import HeaderGenerator

logger = logging.getLogger(__name__)

class DependencyTracker:
    """Tracks dependencies between Python files.
    
    Builds a dependency graph from FORAI headers and updates affected files when dependencies change.
    """
    
    def __init__(self, symbol_registry: SymbolRegistry):
        """Initialize the dependency tracker.
        
        Args:
            symbol_registry: The symbol registry
        """
        self.registry = symbol_registry
        
    def build_dependency_graph(self) -> Dict[str, List[str]]:
        """Build a dependency graph for the entire workspace.
        
        Returns:
            A dictionary mapping file IDs to lists of dependent file IDs
        """
        logger.info("Building dependency graph")
        
        graph = {}
        for file_id, file_info in self.registry.registry['files'].items():
            file_path = os.path.join(self.registry.workspace_path, file_info['path'])
            
            # Skip if file doesn't exist
            if not os.path.exists(file_path):
                continue
                
            # Parse header to get dependencies
            header_imports = self._get_header_imports(file_path)
            
            # Add dependencies to graph
            dependent_files = []
            for imp in header_imports:
                if ':' in imp:
                    dep_file_id = imp.split(':', 1)[0]
                    if dep_file_id and dep_file_id != file_id:  # Skip self-references
                        dependent_files.append(dep_file_id)
            
            graph[file_id] = dependent_files
            
        return graph
    
    def get_affected_files(self, changed_file_id: str) -> List[str]:
        """Get files that depend on the changed file.
        
        Args:
            changed_file_id: The file ID of the changed file
            
        Returns:
            A list of file IDs for files that depend on the changed file
        """
        logger.info(f"Getting affected files for {changed_file_id}")
        
        graph = self.build_dependency_graph()
        affected = []
        
        # Find all files that directly import from the changed file
        for file_id, dependencies in graph.items():
            if changed_file_id in dependencies:
                affected.append(file_id)
                
        return affected
    
    def update_dependent_headers(self, changed_file_id: str, analyzer: StaticAnalyzer, 
                              header_generator: HeaderGenerator, enable_runtime: bool = False) -> None:
        """Update headers in files that depend on the changed file.
        
        Args:
            changed_file_id: The file ID of the changed file
            analyzer: The static analyzer
            header_generator: The header generator
            enable_runtime: Whether to use runtime introspection
        """
        logger.info(f"Updating dependent headers for {changed_file_id}")
        
        affected_files = self.get_affected_files(changed_file_id)
        for file_id in affected_files:
            file_info = self.registry.registry['files'].get(file_id)
            if not file_info:
                continue
                
            file_path = os.path.join(self.registry.workspace_path, file_info['path'])
            
            if os.path.exists(file_path):
                logger.info(f"Updating header for {file_path}")
                
                # Re-analyze the file
                file_data = analyzer.analyze_file(file_path)
                
                # Generate and update header
                header = header_generator.generate_header(file_data)
                header_generator.update_file_header(file_path, header)
    
    def _get_header_imports(self, file_path: str) -> List[str]:
        """Extract imports from a FORAI header.
        
        Args:
            file_path: Path to the file
            
        Returns:
            A list of import references (e.g., "F101:C1")
        """
        try:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read(2000)  # Read first 2000 characters to find header
        except Exception as e:
            logger.error(f"Failed to read file {file_path}: {e}")
            return []
            
        # Look for header
        header_match = re.search(r'//FORAI:(.*?)//', content)
        if not header_match:
            return []
            
        header = header_match.group(1)
        
        # Extract imports
        imp_match = re.search(r'IMP\[(.*?)\]', header)
        if not imp_match:
            return []
            
        imports_str = imp_match.group(1)
        if not imports_str:
            return []
            
        return [imp.strip() for imp in imports_str.split(',')]