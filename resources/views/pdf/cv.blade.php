<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta charset="UTF-8">
    <title>CV {{ $profil['nom'] }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: #333; line-height: 1.5; margin: 0; padding: 20px; }
        h1 { color: #1e293b; margin-bottom: 5px; font-size: 28px; text-transform: uppercase; }
        h2 { color: #4c1d95; font-size: 18px; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-top: 30px; margin-bottom: 15px; text-transform: uppercase; }
        p { margin: 0 0 10px 0; }
        .header { text-align: center; margin-bottom: 30px; }
        .contact-info { color: #64748b; font-size: 12px; margin-bottom: 20px; }
        .section-item { margin-bottom: 15px; }
        .item-title { font-weight: bold; color: #1e293b; font-size: 15px; }
        .item-subtitle { color: #64748b; font-size: 13px; font-style: italic; }
        .item-date { font-weight: bold; color: #4c1d95; font-size: 13px; }
        .badges-container { margin-top: 10px; }
        .badge { display: inline-block; background-color: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; margin-bottom: 5px; border: 1px solid #e2e8f0; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $profil['nom'] }}</h1>
        <p style="font-size: 18px; color: #64748b; font-weight: bold;">{{ $profil['titre'] }}</p>
        <div class="contact-info">
            {{ $profil['email'] }} | {{ $profil['telephone'] }}
        </div>
    </div>

    <!-- DIPLÔMES & CERTIFICATIONS -->
    <h2>Diplômes & Certifications</h2>
    
    @foreach($diplomes as $diplome)
    <div class="section-item">
        <div class="item-title">
            <span class="item-date">[{{ $diplome->year }}]</span> | {{ $diplome->title }}
        </div>
        <div class="item-subtitle">{{ $diplome->institution }}</div>
        @if($diplome->description)
            <p style="font-size: 13px; margin-top: 3px;">{{ $diplome->description }}</p>
        @endif
    </div>
    @endforeach

    @foreach($certifications as $certif)
    <div class="section-item">
        <div class="item-title">
            <span class="item-date">[{{ \Carbon\Carbon::parse($certif->issued_at)->format('Y') }}]</span> | {{ $certif->title }}
        </div>
        <div class="item-subtitle">{{ $certif->organization }}</div>
    </div>
    @endforeach

    <!-- EXPÉRIENCES PROFESSIONNELLES -->
    <h2>Parcours Professionnel</h2>
    @foreach($parcours as $exp)
    <div class="section-item">
        <div class="item-title">{{ $exp->title }} - {{ $exp->organization }}</div>
        <div class="item-subtitle">
            {{ \Carbon\Carbon::parse($exp->date_start)->format('m/Y') }} 
            - 
            {{ $exp->date_end ? \Carbon\Carbon::parse($exp->date_end)->format('m/Y') : 'Présent' }}
        </div>
        <p style="font-size: 13px; margin-top: 5px;">{{ $exp->description }}</p>
    </div>
    @endforeach

    <!-- COMPÉTENCES -->
    <h2>Compétences Techniques</h2>
    <div class="badges-container">
        @foreach($competences as $competence)
            <span class="badge">{{ $competence->name }} ({{ $competence->proficiency }}%)</span>
        @endforeach
    </div>

</body>
</html>
