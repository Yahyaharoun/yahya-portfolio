import glob
import os

files = glob.glob('resources/js/Pages/Admin/**/*.vue', recursive=True)

for f in files:
    # Skip Dashboard.vue so it continues pointing to / (Home)
    if 'Dashboard.vue' in f:
        continue
        
    with open(f, 'r') as file:
        content = file.read()
    
    orig = content
    
    # Replace href="/" with href="/admin/dashboard"
    # Replace title="Retour au site" with title="Retour au Dashboard"
    content = content.replace('href="/"', 'href="/admin/dashboard"')
    content = content.replace('title="Retour au site"', 'title="Retour au Dashboard"')
    
    if content != orig:
        with open(f, 'w') as file:
            file.write(content)
        print("Updated link in", f)

