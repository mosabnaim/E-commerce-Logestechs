import os

def get_folder_structure(rootdir):
    for dirpath, dirnames, filenames in os.walk(rootdir):
        # Ignore '.vscode' directory
        dirnames[:] = [d for d in dirnames if d not in ['.vscode', '.git']]
        
        # Print the directory name
        depth = dirpath.replace(rootdir, '').count(os.sep)
        print('{}{}'.format('  ' * depth, os.path.basename(dirpath) + ':'))

        # Print the files in the directory
        depth += 1
        for f in filenames:
            print('{}- {}'.format('  ' * depth, f))

get_folder_structure(r'C:\xampp\htdocs\logestechs\wp-content\plugins\logestechs')
