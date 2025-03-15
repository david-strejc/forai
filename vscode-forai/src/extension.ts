import * as vscode from 'vscode';
import * as child_process from 'child_process';
import * as path from 'path';

export function activate(context: vscode.ExtensionContext) {
    // Register commands
    const updateHeaderCommand = vscode.commands.registerCommand('forai-header.updateHeader', () => {
        const editor = vscode.window.activeTextEditor;
        if (editor && editor.document.languageId === 'python') {
            updateHeader(editor.document);
        } else {
            vscode.window.showErrorMessage('No active Python file to update FORAI header.');
        }
    });
    
    const updateAllHeadersCommand = vscode.commands.registerCommand('forai-header.updateAllHeaders', () => {
        updateAllHeaders();
    });
    
    // Register file save listener
    const onSaveListener = vscode.workspace.onDidSaveTextDocument((document) => {
        const config = vscode.workspace.getConfiguration('forai-header');
        const updateOnSave = config.get<boolean>('updateOnSave', true);
        
        if (updateOnSave && document.languageId === 'python') {
            updateHeader(document);
        }
    });
    
    // Register file rename listener
    const onRenameListener = vscode.workspace.onDidRenameFiles((event) => {
        for (const file of event.files) {
            if (file.newUri.fsPath.endsWith('.py')) {
                updateHeaderAfterRename(file.oldUri.fsPath, file.newUri.fsPath);
            }
        }
    });
    
    context.subscriptions.push(
        updateHeaderCommand,
        updateAllHeadersCommand,
        onSaveListener,
        onRenameListener
    );
    
    // Helper functions
    async function updateHeader(document: vscode.TextDocument) {
        const workspaceFolder = vscode.workspace.getWorkspaceFolder(document.uri);
        if (!workspaceFolder) {
            vscode.window.showErrorMessage('File must be part of a workspace to update FORAI header.');
            return;
        }
        
        const config = vscode.workspace.getConfiguration('forai-header');
        const enableRuntimeIntrospection = config.get<boolean>('enableRuntimeIntrospection', false);
        
        // Call Python CLI
        try {
            const result = await runForaiCli([
                '--workspace', workspaceFolder.uri.fsPath,
                enableRuntimeIntrospection ? '--runtime' : '',
                'update',
                document.uri.fsPath
            ]);
            
            if (result.success) {
                vscode.window.setStatusBarMessage('FORAI header updated', 3000);
                
                // If imports changed, update dependent files
                if (result.imports_changed) {
                    const depResult = await runForaiCli([
                        '--workspace', workspaceFolder.uri.fsPath,
                        'update-deps',
                        document.uri.fsPath
                    ]);
                    
                    if (depResult.success) {
                        vscode.window.setStatusBarMessage('FORAI header updated in dependent files', 3000);
                    }
                }
            } else {
                vscode.window.showErrorMessage(`Failed to update FORAI header: ${result.error}`);
            }
        } catch (error) {
            vscode.window.showErrorMessage(`Error updating FORAI header: ${error}`);
        }
    }
    
    async function updateAllHeaders() {
        const workspaceFolder = vscode.workspace.workspaceFolders?.[0];
        if (!workspaceFolder) {
            vscode.window.showErrorMessage('No workspace folder found.');
            return;
        }
        
        // Show progress notification
        vscode.window.withProgress({
            location: vscode.ProgressLocation.Notification,
            title: "Updating FORAI headers",
            cancellable: true
        }, async (progress, token) => {
            try {
                const result = await runForaiCli([
                    '--workspace', workspaceFolder.uri.fsPath,
                    'update-all'
                ]);
                
                if (result.success) {
                    vscode.window.showInformationMessage(`Updated FORAI headers for ${result.updated} of ${result.total} files.`);
                } else {
                    vscode.window.showErrorMessage(`Failed to update FORAI headers: ${result.error}`);
                }
            } catch (error) {
                vscode.window.showErrorMessage(`Error updating FORAI headers: ${error}`);
            }
        });
    }
    
    async function updateHeaderAfterRename(oldPath: string, newPath: string) {
        const workspaceFolder = vscode.workspace.getWorkspaceFolder(vscode.Uri.file(newPath));
        if (!workspaceFolder) {
            return;
        }
        
        try {
            const result = await runForaiCli([
                '--workspace', workspaceFolder.uri.fsPath,
                'rename',
                oldPath,
                newPath
            ]);
            
            if (result.success) {
                vscode.window.setStatusBarMessage('FORAI header updated for renamed file', 3000);
            } else {
                vscode.window.showErrorMessage(`Failed to update FORAI header for renamed file: ${result.error}`);
            }
        } catch (error) {
            vscode.window.showErrorMessage(`Error updating FORAI header for renamed file: ${error}`);
        }
    }
    
    async function runForaiCli(args: string[]): Promise<any> {
        return new Promise((resolve, reject) => {
            // Get Python path from settings
            const pythonConfig = vscode.workspace.getConfiguration('python');
            const pythonPath = pythonConfig.get<string>('defaultInterpreterPath', 'python');
            
            // Filter out empty args
            const filteredArgs = args.filter(arg => arg !== '');
            
            // Run forai command
            const process = child_process.spawn(pythonPath, ['-m', 'forai', ...filteredArgs]);
            
            let stdout = '';
            let stderr = '';
            
            process.stdout.on('data', (data) => {
                stdout += data.toString();
            });
            
            process.stderr.on('data', (data) => {
                stderr += data.toString();
            });
            
            process.on('close', (code) => {
                if (code === 0) {
                    try {
                        const result = JSON.parse(stdout);
                        resolve(result);
                    } catch (e) {
                        reject(`Invalid output from FORAI CLI: ${stdout}`);
                    }
                } else {
                    reject(stderr || 'Unknown error');
                }
            });
            
            process.on('error', (error) => {
                reject(`Failed to run FORAI CLI: ${error.message}`);
            });
        });
    }
}

export function deactivate() {}