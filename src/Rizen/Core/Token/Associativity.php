<?php

namespace Rizen\Core\Token;

enum Associativity: string
{
    case LeftToRight = 'Left-to-Right';
    case RightToLeft = 'Right-to-Left';
}
