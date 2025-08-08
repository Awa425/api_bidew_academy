<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat d'achèvement</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* background-image: url('https://www.transparenttextures.com/patterns/cream-paper.png'); */
        }
        .logo {
            max-width: 200px;
            max-height: 100px;
            margin: 0 auto;
        }
        
        .certificate {
            width: 800px;
            height: 600px;
            background-color: white;
            padding: 50px;
            margin-left: 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #182b5c;
        }
        
        .certificate:before, .certificate:after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            border: 8px solid #182b5c;
        }
        
        .certificate:before {
            top: -20px;
            left: -20px;
            border-right: none;
            border-bottom: none;
        }
        
        .certificate:after {
            bottom: -20px;
            right: -20px;
            border-left: none;
            border-top: none;
        }
        
        .corner-decoration {
            position: absolute;
            width: 80px;
            height: 80px;
            opacity: 0.2;
        }
        
        .corner-1 {
            top: 0;
            left: 0;
            border-top: 10px solid #182b5c;
            border-left: 10px solid #182b5c;
        }
        
        .corner-2 {
            top: 0;
            right: 0;
            border-top: 10px solid #182b5c;
            border-right: 10px solid #182b5c;
        }
        
        .corner-3 {
            bottom: 0;
            left: 0;
            border-bottom: 10px solid #182b5c;
            border-left: 10px solid #182b5c;
        }
        
        .corner-4 {
            bottom: 0;
            right: 0;
            border-bottom: 10px solid #182b5c;
            border-right: 10px solid #182b5c;
        }
        
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            color: #5a4a3a;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            display: inline-block;
        }
        
        h1:after {
            content: "";
            position: absolute;
            bottom: -15px;
            left: 25%;
            width: 50%;
            height: 2px;
            background: linear-gradient(to right, transparent, #d4b97a, transparent);
        }
        
        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #3a2e22;
            margin: 30px 0 15px;
            font-weight: 700;
        }
        
        h3 {
            font-size: 20px;
            color: #7a6b59;
            margin: 20px 0;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        .course-name {
            font-size: 24px;
            font-weight: 600;
            margin: 40px 0 15px;
            color: #3a2e22;
            position: relative;
            display: inline-block;
        }
        
        .course-name:before, .course-name:after {
            content: "✧";
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: #d4b97a;
            font-size: 20px;
        }
        
        .course-name:before {
            left: -40px;
        }
        
        .course-name:after {
            right: -40px;
        }
        
        .details {
            font-size: 18px;
            color: #7a6b59;
            margin: 8px 0;
            letter-spacing: 0.5px;
        }
        
        .separator {
            border-top: 1px dashed #d4b97a;
            margin: 40px auto;
            width: 60%;
            position: relative;
        }
        
        .separator:before {
            content: "★";
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #fffdf6;
            padding: 0 15px;
            color: #d4b97a;
            font-size: 20px;
        }
        
        .academy {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: bold;
            margin-top: 30px;
            color: #3a2e22;
            letter-spacing: 1px;
        }
        
        .year {
            font-size: 18px;
            margin-top: 5px;
            color: #7a6b59;
            letter-spacing: 2px;
        }
        
        .stamp {
            position: absolute;
            bottom: 60px;
            right: 60px;
            width: 120px;
            height: 120px;
            background-color: #a82525;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            transform: rotate(15deg);
            opacity: 0.8;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        
        .stamp:before {
            content: "";
            position: absolute;
            width: 90%;
            height: 90%;
            border: 2px dashed white;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="corner-decoration corner-1"></div>
            <div class="corner-decoration corner-2"></div>
            <div class="corner-decoration corner-3"></div>
            <div class="corner-decoration corner-4"></div>

            <div class="logo-container">
                <img src="/public/assets/logo.png" alt="Logo Bideew Technologie" class="logo">
            </div>
            
            <h1>Certificat d'achèvement</h1>
            
            <h2>{{ $user->name }}</h2>
            
            <h3>a bien réussi le cours</h3>
            
            <div class="course-name">{{ $course->title }} {{ $course->objectif }}</div>
            
            <div class="details">Code de certification : {{ $code }}</div>
            <div class="details">{{ \Carbon\Carbon::parse($issued_at)->format('F d, Y') }}</div>
            
            <div class="separator"></div>
            
            <div class="academy">Bideew Technologie INSTRUCTOR</div>
            <!-- <div class="year">2025</div> -->
            
            <!-- <div class="stamp">Certifié<br>Valide</div> -->
        </div>
    </div>
</body>
</html>