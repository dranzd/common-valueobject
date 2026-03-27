<?php

namespace Dranzd\Common\Domain\ValueObject\Money;

enum CurrencyCode: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case GBP = 'GBP';
    case JPY = 'JPY';
    case CNY = 'CNY';
    case PHP = 'PHP';
    case SGD = 'SGD';
    case AUD = 'AUD';
    case CAD = 'CAD';
    case CHF = 'CHF';
    case HKD = 'HKD';
    case NZD = 'NZD';
    case KRW = 'KRW';
    case THB = 'THB';
    case INR = 'INR';
    case MYR = 'MYR';
    case IDR = 'IDR';
    case VND = 'VND';
    case TWD = 'TWD';
    case BRL = 'BRL';
    case MXN = 'MXN';
    case ZAR = 'ZAR';
    case SEK = 'SEK';
    case NOK = 'NOK';
    case DKK = 'DKK';
    case AED = 'AED';
    case SAR = 'SAR';
}
