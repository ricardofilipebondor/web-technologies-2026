#!/usr/bin/env node
/**
 * Validează HTML via W3C Nu Html Checker (validator.w3.org/nu).
 * Usage: node scripts/validate-html.js
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');
const FILES = [
    'frontend/index.html',
    'frontend/app.html',
    'docs/RAPORT.html',
];

async function validate(file) {
    const fullPath = path.join(ROOT, file);
    const body = fs.readFileSync(fullPath);
    const response = await fetch('https://validator.w3.org/nu/?out=json', {
        method: 'POST',
        headers: { 'Content-Type': 'text/html; charset=utf-8' },
        body,
    });
    const data = await response.json();
    return { file, messages: data.messages || [] };
}

async function main() {
    let failed = false;

    for (const file of FILES) {
        const { messages } = await validate(file);
        const errors = messages.filter((m) => m.type === 'error');
        const warnings = messages.filter((m) => m.type !== 'error');

        if (errors.length === 0 && warnings.length === 0) {
            console.log(`OK  ${file}`);
            continue;
        }

        failed = true;
        console.log(`FAIL ${file}`);
        for (const msg of messages) {
            const loc = msg.lastLine ? ` (line ${msg.lastLine})` : '';
            console.log(`  [${msg.type}]${loc} ${msg.message}`);
        }
    }

    process.exit(failed ? 1 : 0);
}

main().catch((err) => {
    console.error(err);
    process.exit(1);
});
