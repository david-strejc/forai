"""Admin model for the sample project."""

from base import BaseModel, Logger
from user import User

class Admin(User):
    """Admin model that represents a system administrator."""
    
    def __init__(self, id=None, name=None, email=None, role=None):
        """Initialize the admin model.
        
        Args:
            id: The admin ID
            name: The admin's name
            email: The admin's email
            role: The admin's role
        """
        super().__init__(id, name, email)
        self.role = role
        
    def validate(self):
        """Validate the admin data."""
        if not super().validate():
            return False
            
        if not self.role:
            Logger.log("Admin role is required")
            return False
            
        return True
        
def create_user(admin, user_data):
    """Create a new user.
    
    Args:
        admin: The admin creating the user
        user_data: The user data
    """
    if not isinstance(admin, Admin):
        Logger.log("Only admins can create users")
        return None
        
    user = User(
        name=user_data.get('name'),
        email=user_data.get('email')
    )
    
    if user.validate():
        Logger.log(f"Admin {admin.name} created user {user.name}")
        user.save()
        return user
    else:
        Logger.log("Failed to create user: Invalid user data")
        return None