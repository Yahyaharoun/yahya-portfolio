import glob

files = glob.glob('database/migrations/*_create_diplomas_table.php')
if files:
    file = files[0]
    with open(file, 'r') as f:
        content = f.read()
    
    # We replace the up() body
    new_up = """
    public function up(): void
    {
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('institution');
            $table->integer('year');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }
"""
    import re
    content = re.sub(r'public function up\(\): void\n\s*\{\n\s*Schema::create\(\'diplomas\', function \(Blueprint \$table\) \{\n\s*\$table->id\(\);\n\s*\$table->timestamps\(\);\n\s*\}\);\n\s*\}', new_up, content, flags=re.MULTILINE)
    
    with open(file, 'w') as f:
        f.write(content)
    print("Migration updated:", file)
