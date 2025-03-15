from setuptools import setup, find_packages

setup(
    name="forai",
    version="0.1.0",
    description="FORAI (File Object Reference for AI Interpretation) header system",
    author="FORAI Team",
    packages=find_packages(),
    py_modules=["root_cli", "root_query"],
    entry_points={
        "console_scripts": [
            "forai=root_cli:main",
            "forai-query=root_query:main",
        ],
    },
    python_requires=">=3.8",
    classifiers=[
        "Development Status :: 3 - Alpha",
        "Intended Audience :: Developers",
        "Programming Language :: Python :: 3",
        "Programming Language :: Python :: 3.8",
        "Programming Language :: Python :: 3.9",
        "Programming Language :: Python :: 3.10",
    ],
)