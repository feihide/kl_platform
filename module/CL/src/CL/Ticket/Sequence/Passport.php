<?php
/**
 * @author：Ethan <ethantsien@gmail.com>
 * @version：$Id: Passport.php 19 2013-11-20 06:20:48Z quanwei $
 */

namespace CL\Ticket\Sequence;

class Passport implements SequenceInterface
{
    public function getName()
    {
        return 'passport';
    }
}
