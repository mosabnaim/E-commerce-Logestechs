import os

def convert_to_camel_case(name):
    return "_".join(word.capitalize() for word in name.split("_"))


# Set your root directory
root_dir = r'C:\xampp\htdocs\logestechs\wp-content\plugins\logestechs'

classes_to_load = {}

# Go through each directory and subdirectory
for root, dirs, files in os.walk(root_dir):
    for file in files:
        if file.endswith(".php"):
            # Remove the root_dir and .php extension, replace slashes with underscores
            class_name = os.path.splitext(file)[0]


            # Generate the relative path to the file from the root directory
            rel_dir = os.path.relpath(root, root_dir)
            
            # Convert directory separators to match PHP's style (if needed)
            rel_dir = rel_dir.replace(os.sep, '/')
            class_name = class_name.replace('-', '_')
                        
            # Convert class name from snake_case to CamelCase
            class_name = convert_to_camel_case(class_name)
            class_name = class_name.replace('Logestechs_', 'Logestechs')

            # Add the class to the corresponding folder's list in the classes_to_load dict
            if rel_dir not in classes_to_load:
                classes_to_load[rel_dir] = []
            class_name = 'Logestechs_' + class_name
            class_name = class_name.replace('Logestechs_Logestechs', 'Logestechs_')

            classes_to_load[rel_dir].append(class_name)

# Print the result
for folder, classes in classes_to_load.items():
    print(f'"{folder}" => array(')
    for class_name in classes:
        print(f'    "{class_name}",')
    print('),')
