"""User model for the sample project."""

from base import BaseModel, Logger

class User(BaseModel):
    """User model that represents a system user."""
    
    def __init__(self, id=None, name=None, email=None):
        """Initialize the user model.
        
        Args:
            id: The user ID
            name: The user's name
            email: The user's email
        """
        super().__init__(id)
        self.name = name
        self.email = email
        
    def validate(self):
        """Validate the user data."""
        if not self.name:
            Logger.log("User name is required")
            return False
            
        if not self.email or '@' not in self.email:
            Logger.log("Valid email is required")
            return False
            
        return True
        
def login(user):
    """Log in a user.
    
    Args:
        user: The user to log in
    """
    if user.validate():
        Logger.log(f"User {user.name} logged in")
        return True
    else:
        Logger.log("Login failed: Invalid user data")
        return False
        
def logout(user):
    """Log out a user.
    
    Args:
        user: The user to log out
    """
    Logger.log(f"User {user.name} logged out")
    return True