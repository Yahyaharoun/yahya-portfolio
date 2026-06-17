<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>CV {{ $admin ? strtoupper($admin->name) : 'YAHYA HAROUN' }}</title>
    <style>
        /* Typography & Base */
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #1f2937; /* text-gray-800 */
            background-color: #ffffff;
            margin: 0;
            padding: 30px 40px;
        }

        * {
            box-sizing: border-box;
        }

        @page {
            margin: 30px 40px 50px 40px; /* Ajout d'une marge en bas pour le footer */
        }

        footer {
            position: fixed; 
            bottom: -30px; 
            left: 0px; 
            right: 0px;
            height: 20px; 
            text-align: center; 
            font-size: 8pt; 
            color: #9ca3af; 
            border-top: 1px solid #e5e7eb; 
            padding-top: 5px;
        }

        /* Utilities */
        .text-accent { color: #6b46c1; /* Violet discret */ }
        .text-muted { color: #6b7280; /* gray-500 */ }
        .font-bold { font-weight: bold; }
        .flex { display: table; width: 100%; }
        .col-left, .col-right { display: table-cell; vertical-align: top; }
        
        /* Layouts */
        .avoid-break { page-break-inside: avoid; }
        .mt-2 { margin-top: 10px; }
        .mt-4 { margin-top: 20px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }

        /* Header */
        .header { text-align: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 15px; margin-bottom: 20px; }
        .name { font-size: 24pt; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #111827; margin-bottom: 5px; }
        .job-title { font-size: 13pt; font-weight: 600; color: #6b46c1; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .contact-info { font-size: 9.5pt; color: #4b5563; }
        .contact-info a { color: #4b5563; text-decoration: none; }
        .contact-separator { margin: 0 6px; color: #d1d5db; }

        /* Section Headings */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            border-bottom: 2px solid #6b46c1;
            padding-bottom: 3px;
            margin-bottom: 12px;
            margin-top: 25px;
            display: inline-block;
        }

        /* Profile */
        .profile-text { text-align: justify; font-size: 10pt; line-height: 1.5; }

        /* Experience & Education Items */
        .item-header { display: table; width: 100%; margin-bottom: 2px; }
        .item-title { display: table-cell; font-weight: bold; font-size: 11pt; color: #1f2937; }
        .item-date { display: table-cell; text-align: right; font-size: 9.5pt; color: #6b46c1; font-weight: 600; white-space: nowrap; }
        .item-subtitle { font-weight: 600; color: #4b5563; font-size: 10pt; margin-bottom: 4px; }
        .item-location { font-style: italic; color: #6b7280; font-size: 9pt; }
        .item-desc { text-align: justify; margin-top: 4px; }
        .item-desc ul { margin-top: 4px; padding-left: 18px; margin-bottom: 0; }
        .item-desc li { margin-bottom: 3px; }

        /* Skills */
        .skills-table { width: 100%; border-collapse: collapse; }
        .skills-category { font-weight: bold; width: 25%; color: #374151; padding: 4px 0; vertical-align: top; }
        .skills-list { width: 75%; padding: 4px 0; color: #4b5563; vertical-align: top; }

        /* Certifications (Two columns) */
        .grid-half { width: 48%; float: left; }
        .grid-right { float: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <!-- FOOTER -->
    <footer>
        CV de {{ $admin ? strtoupper($admin->name) : 'YAHYA HAROUN' }} - Généré automatiquement
    </footer>

    <!-- HEADER -->
    <div class="header">
        <div class="name">Yahya Haroun</div>
        <div class="job-title">Développeur Fullstack & Entrepreneur</div>
        <div class="contact-info">
            <span>Email : <a href="mailto:yahyaharoun.657@gmail.com">yahyaharoun.657@gmail.com</a></span>
            <span class="contact-separator">|</span>
            <span>Tél : +237 690722465</span>
            <span class="contact-separator">|</span>
            <span>Localisation : Cameroun</span>
            <br>
            <span class="mt-2" style="display:inline-block;">
                <a href="https://www.linkedin.com/in/yahya-haroun-87a446344">LinkedIn</a>
                <span class="contact-separator">|</span>
                <a href="https://github.com/Yahyaharoun">GitHub</a>
            </span>
        </div>
    </div>

    <!-- PROFIL -->
    <div class="section-title">Profil</div>
    <div class="profile-text avoid-break mb-4">
        Passionné par l'innovation technologique, je conçois et développe des applications web robustes, scalables et orientées utilisateur. Fort d'une expertise couvrant le développement Full-Stack (Laravel, Vue.js), les solutions d'intelligence artificielle, l'architecture logicielle et l'intégration continue, je m'engage à fournir des solutions techniques modernes (PWA, API, Cloud) répondant aux plus hauts standards de qualité.
    </div>

    <!-- COMPÉTENCES -->
    @if(isset($skillCategories) && $skillCategories->count() > 0)
    <div class="avoid-break mb-4">
        <div class="section-title">Compétences Techniques</div>
        <table class="skills-table">
            @foreach($skillCategories as $category)
            <tr>
                <td class="skills-category">{{ $category->name }}</td>
                <td class="skills-list">
                    {{ $category->skills->pluck('name')->join(' • ') }}
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- EXPÉRIENCES PRO -->
    @if(isset($experiences) && $experiences->count() > 0)
    <div class="section-title">Parcours Professionnel</div>
    <div class="mb-4">
        @foreach($experiences as $exp)
        <div class="avoid-break mb-4">
            <div class="item-header">
                <div class="item-title">{{ $exp->title }}</div>
                <div class="item-date">
                    {{ \Carbon\Carbon::parse($exp->date_start)->translatedFormat('M Y') }} - 
                    {{ $exp->date_end ? \Carbon\Carbon::parse($exp->date_end)->translatedFormat('M Y') : 'Présent' }}
                </div>
            </div>
            <div class="item-subtitle">{{ $exp->company }} <span class="item-location">- {{ $exp->location ?? 'Sur site / Distant' }}</span></div>
            <div class="item-desc">
                {!! nl2br(e($exp->description)) !!}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- FORMATIONS -->
    @if(isset($diplomas) && $diplomas->count() > 0)
    <div class="section-title">Parcours Académique</div>
    <div class="mb-4">
        @foreach($diplomas as $diploma)
        <div class="avoid-break mb-2">
            <div class="item-header">
                <div class="item-title">{{ $diploma->title }}</div>
            </div>
            <div class="item-subtitle">{{ $diploma->institution }} <span class="item-location">- {{ $diploma->location ?? 'Cameroun' }}</span></div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- CERTIFICATIONS & LANGUES -->
    <div class="clearfix">
        @if(isset($certifications) && $certifications->count() > 0)
        <div class="grid-half">
            <div class="section-title">Certifications</div>
            @foreach($certifications as $cert)
            <div class="avoid-break mb-2">
                <div style="font-weight: bold; font-size: 10pt; color: #1f2937;">{{ $cert->title }}</div>
                <div style="font-size: 9pt; color: #6b7280;">{{ $cert->issuer }} | {{ \Carbon\Carbon::parse($cert->issued_at)->format('Y') }}</div>
            </div>
            @endforeach
        </div>
        @endif
        
        <div class="grid-half grid-right">
            <div class="section-title">Langues</div>
            <div class="avoid-break">
                <div style="font-weight: bold; font-size: 10pt; color: #1f2937;">Français</div>
                <div style="font-size: 9pt; color: #6b7280; margin-bottom: 8px;">Langue maternelle (C2)</div>
                <div style="font-weight: bold; font-size: 10pt; color: #1f2937;">Anglais</div>
                <div style="font-size: 9pt; color: #6b7280;">Professionnel technique (B2)</div>
            </div>
        </div>
    </div>
</body>
</html>
