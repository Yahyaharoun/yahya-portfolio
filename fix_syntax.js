const fs = require('fs');
const glob = require('glob');

const files = glob.sync('resources/js/Pages/Admin/**/*.vue');

files.forEach(file => {
  let content = fs.readFileSync(file, 'utf8');
  let original = content;

  // Fix the syntax error:
  // router.delete(`/admin/projects/${id
  // })`)
  
  content = content.replace(/router\.delete\(\`\/(admin\/[a-zA-Z\/]+)\/\$\{id\n\s*\)\`\)/g, "router.delete(`/$1/${id}`)");
  
  // also for deleteCat: router.delete(`/admin/skills/categories/${id\n  })`)
  content = content.replace(/router\.delete\(\`\/(admin\/skills\/categories)\/\$\{id\n\s*\)\`\)/g, "router.delete(`/$1/${id}`)");
  
  if (content !== original) {
    fs.writeFileSync(file, content);
    console.log('Fixed syntax in', file);
  }
});
