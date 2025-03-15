"""Base models for the sample project."""

class BaseModel:
    """Base model class that all other models inherit from."""
    
    def __init__(self, id=None):
        """Initialize the base model.
        
        Args:
            id: The model ID
        """
        self.id = id
        
    def save(self):
        """Save the model to the database."""
        print(f"Saving {self.__class__.__name__} with ID {self.id}")
        
    def validate(self):
        """Validate the model data."""
        return True
        
class Logger:
    """Logger class for logging messages."""
    
    @staticmethod
    def log(message):
        """Log a message.
        
        Args:
            message: The message to log
        """
        print(f"LOG: {message}")