import unittest
import tempfile
import os
import shutil

from forai.symbol_registry import SymbolRegistry
from forai.static_analyzer import StaticAnalyzer
from forai.header_generator import HeaderGenerator


class TestFORAI(unittest.TestCase):
    """Test the FORAI system."""

    def setUp(self):
        """Set up the test environment."""
        # Create a temporary workspace
        self.workspace_path = tempfile.mkdtemp()
        
        # Create test files
        self.create_test_files()
        
        # Initialize components
        self.registry = SymbolRegistry(self.workspace_path)
        self.analyzer = StaticAnalyzer(self.registry)
        self.header_generator = HeaderGenerator(self.registry)
        
    def tearDown(self):
        """Clean up the test environment."""
        # Remove temporary workspace
        shutil.rmtree(self.workspace_path)
        
    def create_test_files(self):
        """Create test Python files."""
        # Create base.py
        base_path = os.path.join(self.workspace_path, 'base.py')
        with open(base_path, 'w') as f:
            f.write("""
class BaseModel:
    def save(self):
        pass
            """)
            
        # Create user.py
        user_path = os.path.join(self.workspace_path, 'user.py')
        with open(user_path, 'w') as f:
            f.write("""
from base import BaseModel

class User(BaseModel):
    def __init__(self, name):
        self.name = name
        
def login(user):
    print(f"Logging in {user.name}")
            """)
            
    def test_static_analysis(self):
        """Test static analysis of a Python file."""
        # Get file paths
        file_path = os.path.join(self.workspace_path, 'user.py')
        
        # Analyze the file
        file_data = self.analyzer.analyze_file(file_path)
        
        # Check file_id
        self.assertIn('file_id', file_data)
        
        # Check definitions
        self.assertIn('definitions', file_data)
        definitions = file_data['definitions']
        self.assertEqual(len(definitions), 2)  # User class and login function
        
        # Check imports
        self.assertIn('imports', file_data)
        
        # Check exports
        self.assertIn('exports', file_data)
        
    def test_header_generation(self):
        """Test generation of a FORAI header."""
        # Get file paths
        file_path = os.path.join(self.workspace_path, 'user.py')
        
        # Analyze the file
        file_data = self.analyzer.analyze_file(file_path)
        
        # Generate header
        header = self.header_generator.generate_header(file_data)
        
        # Check header format
        self.assertIn('//FORAI:', header)
        self.assertIn('DEF[', header)
        self.assertIn('IMP[', header)
        self.assertIn('EXP[', header)
        
    def test_header_update(self):
        """Test updating a file with a FORAI header."""
        # Get file paths
        file_path = os.path.join(self.workspace_path, 'user.py')
        
        # Analyze the file
        file_data = self.analyzer.analyze_file(file_path)
        
        # Generate header
        header = self.header_generator.generate_header(file_data)
        
        # Update the file
        self.header_generator.update_file_header(file_path, header)
        
        # Check if the header was added
        with open(file_path, 'r') as f:
            content = f.read()
        
        self.assertIn('//FORAI:', content)


if __name__ == '__main__':
    unittest.main()