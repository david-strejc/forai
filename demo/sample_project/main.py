"""Main module for the sample project."""

from base import Logger
from user import User, login, logout
from admin import Admin, create_user

def main():
    """Main entry point for the sample project."""
    # Create an admin
    admin = Admin(
        id=1,
        name="Admin User",
        email="admin@example.com",
        role="super_admin"
    )
    
    # Create a regular user
    user_data = {
        'name': 'Regular User',
        'email': 'user@example.com'
    }
    user = create_user(admin, user_data)
    
    # Login the user
    if user and login(user):
        # Do something with the user
        Logger.log(f"User {user.name} is doing something")
        
        # Logout the user
        logout(user)
        
if __name__ == '__main__':
    main()