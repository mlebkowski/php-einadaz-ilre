<?php
declare(strict_types=1);    

function getAndExhaust(\Generator $generator): array | null {
    $current = $generator->current();
    $generator->next();
    return $current;
}
    
function createMissingReport(\Generator $alpha, \Generator $bravo): array {
    if ($alpha->current()['id'] < $bravo->current()['id']) {
        return [getAndExhaust($alpha), null];
    }
    return [null, getAndExhaust($bravo)];
}
    
function getDifferingPairs(\Iterator $cursorAlpha, \Iterator $cursorBravo): \Generator
{
    // We will advance the two cursor simultaneously, and the algorithm
    // will decide which one to move, and which to keep. We can illustrate
    // it as two tapes moving along, and at any given moment we only keep
    // one record from each in memory, so the memory consumption is constant
    // regardless of the stream sizes
    do {
        //       | Alpha   | Bravo   |
        //    -> | [1]     | 1       |
        //       | 2       |           <-
        //       | 3       |
        // 
        //       | Alpha   | Bravo   |
        //       | 1       | 1       |
        //       | 2       | [2]     | <-
        //    ->           | 3       |
        // 
        // at least one stream ended. this means that we need to yield anything
        // that is left in the other one. we can take advantage of the fact that
        // `current()` on an invalid generator returns `null` -- exactly what we 
        // expect in our output. We can also `next()` it despite it being already
        // invalid. That means we can just `getAndExhaust()` both the same way
        if ($cursorAlpha->valid() ^ $cursorBravo->valid()) {
            yield [getAndExhaust($cursorAlpha), getAndExhaust($cursorBravo)];

            // continue here allows us not to stack elseifs. it’s a style choice
            continue;
        }

        // there are still items in both generators, let’s compare them
        //       | Alpha   | Bravo   |
        //       | ...     | ...     | 
        //       | ...     | [...]   | <-
        //       | ...     | ...     | 
        //    -> | [...]   | ...     | 
        //       | ...     | ...     | 
        
        $alphaValue = $cursorAlpha->current();
        $bravoValue = $cursorBravo->current();

        if ($alphaValue['id'] !== $bravoValue['id']) {
            // if the ids are different, we need to report the product is missing in
            // one of the sources. one of them will have a smaller id, and we will
            // pair that with a null, and advance that track for the next iteration
            //       | Alpha   | Bravo   |
            //       | ...     | ...     | 
            //    -> | [9]     | ...     |
            //       | ...     | 6       | --.
            //       | ...     | [...]   | <-'
            //       | ...     | ...     | 
            yield createMissingReport($cursorAlpha, $cursorBravo);
            // we use continue, but easily we could’ve used `else`
            continue;
        }
        
        // there are two remaining scenarios. in both of them the IDs are equal,
        // but in one of them the details differ. only then do we yield
        //       | Alpha   | Bravo   |
        //       | ...     | ...     | 
        //       | ...     | 9       | --.
        //       | ...     | [...]   | <-'
        //   .-- | 9       | ...     |
        //   '-> | [...]   | ...     | 
        //       | ...     | ...     |
        if ($alphaValue !== $bravoValue) {
            yield [$alphaValue, $bravoValue];
        }

        // regardless of the differences, we advance both cursors
        $cursorAlpha->next();
        $cursorBravo->next();
        
    // work until both generators yielded all records
    } while ($cursorBravo->valid() || $cursorAlpha->valid());
}
    