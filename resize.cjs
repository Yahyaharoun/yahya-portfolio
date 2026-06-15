const sharp = require('sharp');
const fs = require('fs');

async function resize() {
  const input = '/home/yahya-haroun/.gemini/antigravity/brain/605500ca-8ae3-4bea-9d73-c2075c904043/yh_logo_1781563440428.png';
  
  await sharp(input).resize(512, 512).toFile('public/icons/icon-512x512.png');
  await sharp(input).resize(384, 384).toFile('public/icons/icon-384x384.png');
  await sharp(input).resize(192, 192).toFile('public/icons/icon-192x192.png');
  console.log('Resized successfully');
}
resize().catch(console.error);
