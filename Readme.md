# Optimization challenge

## Requirements

PHP 8.2. Probably works on earlier versions too, but I haven’t checked.

## Run the script

Simply: `php job.php`

## Tests

You need composer. After installing dependencies, simply execute `composer run test`

## Other notes

I tried for the final solution not only to be efficient, but also readable. I did my
best, but I think I could’ve put it to the next level with some OOP around those generators,
the two tapes abstraction, and a state machine on top. Readability is a subjective matter
either way, so I wasn’t pushing it without context.

As usual with this kind of refactoring tasks, I started with a test first, so I had
the requirements formally verified, and then I could freely change the implementation
knowing that I did not break the result.
Naturally, the order of the results had to change, because to optimize the memory usage
there was no way to iterate over the generators in the same order as in the dummy version.

I had to code it in an editor, because I don’t have my IDE. That was not a pleasant experience. :-/
