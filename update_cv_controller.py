import re

with open('app/Http/Controllers/CvController.php', 'r') as f:
    content = f.read()

# 1. Add imports
imports = """use Illuminate\Support\Facades\Cache;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Certification;
use App\Models\Diploma;
use Barryvdh\DomPDF\Facade\Pdf;"""

content = content.replace("use Illuminate\Support\Facades\Cache;", imports)

# 2. Update processDownload
old_process = """
        // Autoriser le téléchargement
        return back()->with('success', 'Formulaire validé, téléchargement autorisé.');
    }
}
"""

new_process = """
        // Données dynamiques
        $profil = [ 
            'nom' => 'YAHYA HAROUN', 
            'email' => 'Yahyaharoun.657@gmail.com', 
            'telephone' => '+237 690722465', 
            'titre' => 'Etudiant_Tech & Entrepreneur Tech' 
        ];
        $competences = Skill::all();
        $parcours = Experience::orderBy('date_debut', 'desc')->get();
        $certifications = Certification::all();
        $diplomes = Diploma::orderBy('year', 'desc')->get();

        // Génération du PDF
        $pdf = Pdf::loadView('pdf.cv', compact('profil', 'competences', 'parcours', 'certifications', 'diplomes'));
        
        return $pdf->download('CV_YAHYA_HAROUN.pdf');
    }
}
"""
content = content.replace(old_process, new_process)

with open('app/Http/Controllers/CvController.php', 'w') as f:
    f.write(content)

print("CvController updated")
