<?php

namespace Database\Seeders;

use App\Models\CertificateTemplate;
use Illuminate\Database\Seeder;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Template 1: Classic
        CertificateTemplate::updateOrCreate(
            ['name' => 'Classic', 'is_default' => true],
            [
            'description' => 'Traditional certificate design with elegant borders',
            'type' => 'default',
            'is_active' => true,
            'html_template' => '
            <div class="certificate-container">
                <div class="certificate-title">{{TITLE}}</div>
                <div class="certificate-subtitle">{{SUBTITLE}}</div>
                <div class="participant-name">{{PARTICIPANT_NAME}}</div>
                <div class="event-details">
                    {{BODY_TEXT}}<br>
                    <strong>{{EVENT_NAME}}</strong><br>
                    {{#EVENT_LOCATION}}held at {{EVENT_LOCATION}}<br>{{/EVENT_LOCATION}}
                    {{#EVENT_DATE}}on {{EVENT_DATE}}<br>{{/EVENT_DATE}}
                </div>
                <div class="issued-date">
                    Issued on {{ISSUED_DATE}}
                </div>
                <div class="certificate-id">
                    Certificate ID: {{CERTIFICATE_ID}}
                </div>
                {{QR_CODE}}
            </div>',
            'css_styles' => '
            @page { margin: 0; size: A4 landscape; }
            body {
                font-family: "Times New Roman", serif;
                margin: 0;
                padding: 0;
                background: transparent;
            }
            .certificate-container {
                position: absolute;
                top: 16mm; right: 16mm; bottom: 16mm; left: 16mm;
                background: white;
                text-align: center;
                box-sizing: border-box;
                overflow: hidden;
                border: 0 !important;
                box-shadow: inset 0 0 0 3mm #d4af37;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .certificate-title {
                font-size: 48px;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 20px;
                text-transform: uppercase;
                letter-spacing: 3px;
            }
            .certificate-subtitle {
                font-size: 24px;
                color: #7f8c8d;
                margin-bottom: 40px;
            }
            .participant-name {
                font-size: 36px;
                font-weight: bold;
                color: #2c3e50;
                margin: 40px 0;
                padding: 20px;
                border-bottom: 3px solid #d4af37;
                display: inline-block;
            }
            .event-details {
                font-size: 18px;
                color: #34495e;
                margin: 30px 0;
                line-height: 1.8;
            }
            .certificate-id {
                font-size: 14px;
                color: #95a5a6;
                margin-top: 40px;
            }
            .qr-code {
                position: absolute;
                bottom: 30px;
                right: 30px;
                width: 120px;
                height: 120px;
            }
            .issued-date {
                font-size: 16px;
                color: #7f8c8d;
                margin-top: 20px;
            }',
            'customization_settings' => [
                'title_text' => 'Certificate of Participation',
                'subtitle_text' => 'This is to certify that',
                'body_text' => 'has successfully participated in',
                'title_font' => 'Times New Roman',
                'subtitle_font' => 'Times New Roman',
                'name_font' => 'Times New Roman',
                'body_font' => 'Times New Roman',
                'title_font_size' => '48px',
                'subtitle_font_size' => '24px',
                'name_font_size' => '36px',
                'body_font_size' => '18px',
                'title_color' => '#2c3e50',
                'subtitle_color' => '#7f8c8d',
                'name_color' => '#2c3e50',
                'body_color' => '#34495e',
            ],
        ]);

        // Template 2: Modern
        CertificateTemplate::updateOrCreate(
            ['name' => 'Modern', 'is_default' => true],
            [
            'description' => 'Clean and modern design with minimalist style',
            'type' => 'default',
            'is_active' => true,
            'html_template' => '
            <div class="certificate-container">
                <div class="certificate-title">{{TITLE}}</div>
                <div class="certificate-subtitle">{{SUBTITLE}}</div>
                <div class="participant-name">{{PARTICIPANT_NAME}}</div>
                <div class="event-details">
                    {{BODY_TEXT}}<br>
                    <strong>{{EVENT_NAME}}</strong><br>
                    {{#EVENT_LOCATION}}{{EVENT_LOCATION}}<br>{{/EVENT_LOCATION}}
                    {{#EVENT_DATE}}{{EVENT_DATE}}{{/EVENT_DATE}}
                </div>
                <div class="issued-date">Issued on {{ISSUED_DATE}}</div>
                <div class="certificate-id">ID: {{CERTIFICATE_ID}}</div>
                {{QR_CODE}}
            </div>',
            'css_styles' => '
            @page { margin: 0; size: A4 landscape; }
            body {
                font-family: "Arial", sans-serif;
                margin: 0;
                padding: 0;
                background: transparent;
            }
            .certificate-container {
                position: absolute;
                top: 16mm; right: 16mm; bottom: 16mm; left: 16mm;
                background: white;
                text-align: center;
                box-sizing: border-box;
                overflow: hidden;
                border: 0 !important;
                box-shadow: inset 0 0 0 1mm #333;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .certificate-title {
                font-size: 42px;
                font-weight: 300;
                color: #333;
                margin-bottom: 30px;
                letter-spacing: 2px;
            }
            .certificate-subtitle {
                font-size: 20px;
                color: #666;
                margin-bottom: 50px;
            }
            .participant-name {
                font-size: 40px;
                font-weight: 600;
                color: #000;
                margin: 50px 0;
                padding: 30px 0;
                border-top: 1px solid #ddd;
                border-bottom: 1px solid #ddd;
            }
            .event-details {
                font-size: 16px;
                color: #555;
                margin: 40px 0;
                line-height: 2;
            }
            .certificate-id {
                font-size: 12px;
                color: #999;
                margin-top: 50px;
            }
            .qr-code {
                position: absolute;
                bottom: 30px;
                right: 30px;
                width: 100px;
                height: 100px;
            }
            .issued-date {
                font-size: 14px;
                color: #777;
                margin-top: 30px;
            }',
            'customization_settings' => [
                'title_text' => 'Certificate of Achievement',
                'subtitle_text' => 'This certifies that',
                'body_text' => 'has successfully completed',
                'title_font' => 'Arial',
                'subtitle_font' => 'Arial',
                'name_font' => 'Arial',
                'body_font' => 'Arial',
                'title_font_size' => '42px',
                'subtitle_font_size' => '20px',
                'name_font_size' => '40px',
                'body_font_size' => '16px',
                'title_color' => '#333',
                'subtitle_color' => '#666',
                'name_color' => '#000',
                'body_color' => '#555',
            ],
        ]);

        // Template 3: Elegant
        CertificateTemplate::updateOrCreate(
            ['name' => 'Elegant', 'is_default' => true],
            [
            'description' => 'Sophisticated design with decorative elements',
            'type' => 'default',
            'is_active' => true,
            'html_template' => '
            <div class="certificate-container">
                <div class="certificate-title">{{TITLE}}</div>
                <div class="certificate-subtitle">{{SUBTITLE}}</div>
                <div class="participant-name">{{PARTICIPANT_NAME}}</div>
                <div class="event-details">
                    {{BODY_TEXT}}<br>
                    <strong>{{EVENT_NAME}}</strong><br>
                    {{#EVENT_LOCATION}}at {{EVENT_LOCATION}}<br>{{/EVENT_LOCATION}}
                    {{#EVENT_DATE}}on {{EVENT_DATE}}{{/EVENT_DATE}}
                </div>
                <div class="issued-date">Issued on {{ISSUED_DATE}}</div>
                <div class="certificate-id">Certificate ID: {{CERTIFICATE_ID}}</div>
                {{QR_CODE}}
            </div>',
            'css_styles' => '
            @page { margin: 0; size: A4 landscape; }
            body {
                font-family: "Georgia", serif;
                margin: 0;
                padding: 0;
                background: transparent;
            }
            .certificate-container {
                position: absolute;
                top: 16mm; right: 16mm; bottom: 16mm; left: 16mm;
                background: white;
                text-align: center;
                box-sizing: border-box;
                overflow: hidden;
                border: 0 !important;
                box-shadow: inset 0 0 0 3mm #8b7355;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
            .ornament-top, .ornament-bottom {
                font-size: 30px;
                color: #8b7355;
                margin: 20px 0;
            }
            .certificate-title {
                font-size: 50px;
                font-weight: bold;
                color: #2c1810;
                margin-bottom: 25px;
                text-transform: uppercase;
                letter-spacing: 4px;
                font-style: italic;
            }
            .certificate-subtitle {
                font-size: 22px;
                color: #5a4a3a;
                margin-bottom: 45px;
                font-style: italic;
            }
            .participant-name {
                font-size: 38px;
                font-weight: bold;
                color: #2c1810;
                margin: 45px 0;
                padding: 25px 0;
                border-bottom: 2px solid #8b7355;
                display: inline-block;
            }
            .event-details {
                font-size: 19px;
                color: #4a3a2a;
                margin: 35px 0;
                line-height: 2;
                font-style: italic;
            }
            .certificate-id {
                font-size: 13px;
                color: #8b7355;
                margin-top: 45px;
            }
            .qr-code {
                position: absolute;
                bottom: 25px;
                right: 25px;
                width: 110px;
                height: 110px;
            }
            .issued-date {
                font-size: 15px;
                color: #5a4a3a;
                margin-top: 25px;
            }',
            'customization_settings' => [
                'title_text' => 'Certificate of Excellence',
                'subtitle_text' => 'Be it known that',
                'body_text' => 'has demonstrated outstanding achievement in',
                'title_font' => 'Georgia',
                'subtitle_font' => 'Georgia',
                'name_font' => 'Georgia',
                'body_font' => 'Georgia',
                'title_font_size' => '50px',
                'subtitle_font_size' => '22px',
                'name_font_size' => '38px',
                'body_font_size' => '19px',
                'title_color' => '#2c1810',
                'subtitle_color' => '#5a4a3a',
                'name_color' => '#2c1810',
                'body_color' => '#4a3a2a',
            ],
        ]);
    }
}
