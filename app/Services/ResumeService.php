<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class ResumeService
{
    protected GeminService $gemini;

    public function __construct(GeminService $service)
    {
        $this->gemini = $service;
    }
    public function uploadFile($file)
    {
        // get file original name
        $originalName = $file->getClientOriginalName();
        $extension = $file->extension();
        // make new file path 
        $newName = "res" . Str::random(5) . time() . "." . $extension;
        // store file in cloud 
        $file->storeAs("resumes", $newName, "s3");

        $storagePath = "resumes/" . $newName;

        // extract file content
        $prompt = $this->extractFileContent($file);
        // use ai for extracting text data
        $resume_data = $this->getCleanAiData($prompt);
        // store Resume in DB

       $resume= $this->storeInDB($originalName, $storagePath, $resume_data);

       return $resume;
    }

    protected function extractFileContent($file)
    {

        // get file data
        $tempFile = tempnam(sys_get_temp_dir(), "resumes");

        $fileContent = $file->getContent();

        file_put_contents($tempFile, $fileContent);

        // extract file content
        $parser = new Parser();

        $pdf = $parser->parseFile($tempFile);

        $text = $pdf->getText();



        $cvText = $text;

        $prompt = '
Extract the following information from this CV and return it strictly as a JSON object.
Do not include any Markdown formatting, code blocks, or additional explanations. The output must be valid, parseable JSON.

The JSON object must have exactly the following keys, with string values:
{
    "skills": "extracted skills here",
    "summary": "extracted summary here",
    "experience": "extracted experience here",
    "education": "extracted education here"
}

CV:
' . $cvText;

        return $prompt;
    }

    protected function storeInDB(string $org_name,string $new_path, array $resume_data)
    {
        $resume = Resume::create([
            "file_name" => $org_name,
            "file_url" => $new_path,
            "contract_details" => " ",
            'skills'=>$resume_data["skills"],
            'summary'=>$resume_data["summary"],
            'experience'=>$resume_data["experience"],
            'education'=>$resume_data["education"],
            'user_id'=>Auth::user()->id,
        ]);


        return $resume;
    }

    protected function getCleanAiData(string $prompt)
    {
        $response = $this->gemini->Response($prompt)->json();

        $text = $response["candidates"][0]["content"]["parts"][0]["text"] ?? '{}';

        // Sometimes the AI wraps it in markdown despite our prompt, so we strip it.
        $text = str_replace(['```json', '```'], '', $text);
        $text = str_replace("\n", ' ', $text);

        $data = json_decode(trim($text), true) ?? [];

        return [
            "summary" => $data["summary"] ?? "",
            "skills" => $data["skills"] ?? "",
            "experience" => $data["experience"] ?? "",
            "education" => $data["education"] ?? "",
        ];
    }
}
