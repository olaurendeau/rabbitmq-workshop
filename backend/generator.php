<?php

// Usage :
// > generator.php heavy.pdf
// Will generate an heavy.pdf file

// Simulate slowness
sleep(3);

// Generate PDF
copy(sprintf('%s/shared/Invoice_Template.pdf', __DIR__), sprintf('%s/shared/%s', __DIR__, $argv[1]));

// Output file uri
echo sprintf('/shared/%s', $argv[1]);
