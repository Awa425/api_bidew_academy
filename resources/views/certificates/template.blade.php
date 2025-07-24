

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 0cm 0cm; size: A4 landscape;}
        body {
            margin: 2cm;
            font-family: DejaVu Sans, sans-serif;
            background: #fff;
            color: #000;
        }

        .certificate-border {
            border: 12px double #d4af37;
            padding: 40px;
            height: 100%;
            /* position: relative; */
            border-style: groove;
        }

        .ribbon {
            position: absolute;
            top: -20px;
            left: -20px;
        }

        .ribbon .medal {
            width: 60px;
            height: 60px;
            background: radial-gradient(circle, gold 60%, orange 90%);
            border-radius: 50%;
            position: relative;
            box-shadow: 0 0 5px #888;
        }

        h1 {
            text-align: center;
            font-size: 32px;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        h2 {
            text-align: center;
            font-size: 18px;
            margin-top: 0;
            font-weight: normal;
            letter-spacing: 2px;
        }

        .content {
            text-align: center;
            margin-top: 50px;
            line-height: 1.8;
        }

        .recipient {
            font-size: 26px;
            font-weight: bold;
            margin-top: 20px;
        }

        .course-title {
            font-weight: bold;
            font-size: 18px;
        }

        .signature {
            /* margin-top: 80px; */
            text-align: center;
        }

        .signature hr {
            width: 200px;
            margin: 0 auto 5px;
        }

        .signature p {
            margin: 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate-border">
        <div class="ribbon">
            <div class="medal"></div>
        </div>

        <h1>Certificat de Réussite  </h1> <h2>delivré par</h2>
        <h2><strong>Bideew Technologie</strong> </h2>

        <div class="content">
            <p>Ce certificat est décerné à</p>
            <div class="recipient">{{ $user->name }}</div>

            <p>pour avoir complété avec succès le module</p>
            <div class="course-title">{{ $course->title }}</div>

            <p><strong>{{ \Carbon\Carbon::parse($issued_at)->format('F d, Y') }}</strong></p>
            <p>Code de certification : {{ $code }}</p>
        </div>

        <div class="signature">
            <hr>
            <p>Bidew Technologie</p>
            <p>INSTRUCTOR</p>
        </div>
    </div>
</body>
</html>
