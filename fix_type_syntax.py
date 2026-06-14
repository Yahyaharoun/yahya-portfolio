import glob

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    
    content = content.replace("onConfirm: () => {} as () => void", "onConfirm: (() => {}) as any")
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Fixed type syntax in", f)

