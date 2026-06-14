import re

with open('resources/js/Layouts/AuthenticatedLayout.vue', 'r') as f:
    content = f.read()

# Add BookOpenIcon to outline imports
content = content.replace("AcademicCapIcon,", "AcademicCapIcon,\n  BookOpenIcon,")
content = content.replace("AcademicCapIcon as AcademicSolid,", "AcademicCapIcon as AcademicSolid,\n  BookOpenIcon as BookOpenSolid,")

# Add to navItems
nav_item_diplomas = "  { label: 'Diplômes',       href: '/admin/diplomas',      routeName: 'admin.diplomas.index',  icon: BookOpenIcon,  iconActive: BookOpenSolid    },"
content = content.replace("  { label: 'Certifications', href: '/admin/certifications',routeName: 'admin.certifications',  icon: AcademicCapIcon, iconActive: AcademicSolid    },", 
                          "  { label: 'Certifications', href: '/admin/certifications',routeName: 'admin.certifications',  icon: AcademicCapIcon, iconActive: AcademicSolid    },\n" + nav_item_diplomas)

with open('resources/js/Layouts/AuthenticatedLayout.vue', 'w') as f:
    f.write(content)

print("Sidebar updated")
