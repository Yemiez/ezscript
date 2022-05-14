<?php

namespace Core\Lex\Token;

enum OperatorType: string
{
    case ScopeResolution = '::'; // 1, ltr
    case SuffixIncrement = '++'; // 1, ltr
    case PostfixIncrement = '++'; // 1, ltr
    case SuffixDecrement = '--'; // 1, ltr
    case PostfixDecrement = '--'; // 1, ltr
    case FunctionCall = 'a()'; // 1, ltr
    case Subscript = 'a[]'; // 1, ltr
    case MemberAccess = '.'; // 1, ltr/rtl

    case UnaryPlus = '+'; // 2, rtl
    case UnaryMinus = '-'; // 2, rtl
    case LogicalNot = '!'; // 2, rtl
    case BitwiseNot = '~'; // 2 ,rtl
    case Cast = '(type)'; // 2, rtl
    case Indirection = '*a'; // 2, rtl
    case AddressOf = '&a'; // 2, rtl

    case Multiplication = 'a*b'; // 5, ltr
    case Division = 'a/b'; // 5, ltr
    case Remainder = 'a%b'; // 5, ltr

    case Addition = 'a+b'; // 6, ltr
    case Subtraction = 'a-b'; // 6, ltr
    case BitwiseLeftShift = '<<'; // 7, ltr
    case BitwiseRightShift = '>>'; // 7, ltr
    case ThreeWayComparisonOperator = '<==>'; // 8, ltr
    case LessThan = '<'; // 9, ltr
    case LessThanEqual = '<='; // 9, ltr
    case GreaterThan = '>'; // 9 ltr
    case GreaterThanEqual = '>='; // 9, ltr
    case Equals = '='; // 10, lr
    case NotEquals = '!='; // 10, ltr
    case BitwiseAnd = '&'; // 11, ltr
    case BitwiseXor = '^'; // 12, ltr
    case BitwiseOr = '|'; // 13, ltr
    case LogicalAnd = '&&'; // 14, ltr
    case LogicalOr = '||'; // 15, ltr
    case TernaryConditional = 'a?b:c'; // 16 rtl
    case Throw = 'throw'; // 16 rtl
    case DirectAssignment = '_='; // 16, rtl
    case CompoundAddition = '+='; // 16, rtl
    case CompoundAssignmentDifference = '-='; // 16, rtl
    case CompoundAssignmentProduct = '*='; // 16, rtl
    case CompoundAssignmentQuotient = '/='; // 16, rtl
    case CompoundAssignmentRemainder = '%='; // 16, rtl
    case CompoundAssignmentBitwiseLeftShift = '<<='; // 16, rtl
    case CompoundAssignmentBitwiseRightShift = '>>='; // 16, rtl
    case CompoundAssignmentBitwiseAnd = '&='; // 16, rtl
    case CompoundAssignmentBitwiseXor = '^='; // 16, rtl
    case CompoundAssignmentBitwiseOr = '|='; // 16, rtl
    case Comma = ','; // 16, rtl

    public function associativity(): Associativity
    {
        return match ($this) {
            self::ScopeResolution, self::MemberAccess, self::Subscript, self::FunctionCall,
            self::PostfixDecrement, self::SuffixDecrement, self::PostfixIncrement,
            self::SuffixIncrement, self::Remainder, self::Division, self::Multiplication,
            self::LogicalOr, self::LogicalAnd, self::BitwiseOr, self::BitwiseXor,
            self::BitwiseAnd, self::NotEquals, self::Equals, self::GreaterThanEqual,
            self::GreaterThan, self::LessThanEqual, self::LessThan,
            self::ThreeWayComparisonOperator, self::BitwiseRightShift,
            self::BitwiseLeftShift, self::Subtraction, self::Addition,
            self::Comma => Associativity::LeftToRight,

            self::UnaryPlus, self::AddressOf, self::Indirection, self::Cast,
            self::BitwiseNot, self::LogicalNot, self::UnaryMinus,
            self::CompoundAssignmentBitwiseOr,
            self::CompoundAssignmentBitwiseXor,
            self::CompoundAssignmentBitwiseAnd,
            self::CompoundAssignmentBitwiseRightShift,
            self::CompoundAssignmentBitwiseLeftShift,
            self::CompoundAssignmentRemainder,
            self::CompoundAssignmentQuotient,
            self::CompoundAssignmentProduct,
            self::CompoundAssignmentDifference, self::CompoundAddition,
            self::DirectAssignment, self::Throw, self::TernaryConditional => Associativity::RightToLeft
        };
    }

    public function precedence(): int
    {
        return match ($this) {
            self::ScopeResolution => 1,
            self::SuffixIncrement => 2,

            self::PostfixIncrement => 2,
            self::SuffixDecrement => 2,
            self::PostfixDecrement => 2,
            self::FunctionCall => 2,
            self::Subscript => 2,
            self::MemberAccess => 2,

            self::UnaryPlus => 3,
            self::UnaryMinus => 3,
            self::LogicalNot => 3,
            self::BitwiseNot => 3,
            self::Cast => 3,
            self::Indirection => 3,
            self::AddressOf => 3,

            self::Multiplication => 4,
            self::Division => 4,
            self::Remainder => 4,

            self::Addition => 5,
            self::Subtraction => 5,

            self::BitwiseLeftShift => 6,
            self::BitwiseRightShift => 6,

            self::ThreeWayComparisonOperator => 7,

            self::LessThan => 8,
            self::LessThanEqual => 8,
            self::GreaterThan => 8,
            self::GreaterThanEqual => 8,

            self::Equals => 9,
            self::NotEquals => 9,

            self::BitwiseAnd => 10,

            self::BitwiseXor => 11,

            self::BitwiseOr => 12,

            self::LogicalAnd => 13,

            self::LogicalOr => 14,

            self::TernaryConditional => 15,
            self::Throw => 15,
            self::DirectAssignment => 15,
            self::CompoundAddition => 15,
            self::CompoundAssignmentDifference => 15,
            self::CompoundAssignmentProduct => 15,
            self::CompoundAssignmentQuotient => 15,
            self::CompoundAssignmentRemainder => 15,
            self::CompoundAssignmentBitwiseLeftShift => 15,
            self::CompoundAssignmentBitwiseRightShift => 15,
            self::CompoundAssignmentBitwiseAnd => 15,
            self::CompoundAssignmentBitwiseXor => 15,
            self::CompoundAssignmentBitwiseOr => 15,

            self::Comma => 16,
        };
    }
}
