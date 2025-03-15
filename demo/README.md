# FORAI Demo

This directory contains a simple demo to show how FORAI works.

## Sample Project

The sample project is a simple user management system with the following files:

- `base.py`: Contains base models (BaseModel, Logger)
- `user.py`: Contains the User model and user-related functions
- `admin.py`: Contains the Admin model and admin-related functions
- `main.py`: Main entry point for the sample project

## Running the Demo

To run the demo, follow these steps:

1. Install FORAI:

   ```bash
   pip install -e ../
   ```

2. Generate FORAI headers for all Python files:

   ```bash
   cd sample_project
   forai --workspace . update-all
   ```

3. View the generated headers:

   ```bash
   cat base.py
   cat user.py
   cat admin.py
   cat main.py
   ```

4. Query the FORAI headers:

   ```bash
   # Find where User is defined
   forai-query --workspace . find User

   # Get all symbols defined in user.py
   forai-query --workspace . file-symbols user.py

   # Find all files that use Logger
   forai-query --workspace . usages Logger

   # List all symbols in the workspace
   forai-query --workspace . list
   ```

5. Make a change to one of the files (e.g., add a new method to `User`) and update the headers:

   ```bash
   forai --workspace . update user.py
   ```

6. See how FORAI automatically updates dependent files:

   ```bash
   cat admin.py # Should have an updated header
   ```

This demo shows how FORAI helps AI assistants understand code structure without parsing entire files.