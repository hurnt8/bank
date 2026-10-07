<?php

namespace App\Services;

use App\Models\AccountMovement;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\Transfer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Facture / justificatif PDF d'une opération sur le compte (crédit, débit, virement), joint aux e-mails
 * envoyés au client. Rédigé dans la langue du client, avec le logo du site.
 */
class OperationPdf
{
    public static function forMovement(AccountMovement $m): string
    {
        $m->loadMissing(['client', 'admin']);

        return self::render($m->client, fn (string $loc) => self::movementDoc($m, $loc));
    }

    /** Données du document pour un mouvement de compte (langue courante). */
    public static function movementDoc(AccountMovement $m, string $loc): array
    {
        $client = $m->client;

        {
            $credit = $m->type === 'credit';
            $party  = $m->kind === 'card' ? __('movement.label_merchant') : ($credit ? __('movement.label_from') : __('movement.label_to'));
            $card   = $m->card_id ? Card::find($m->card_id) : null;
            $iban   = optional($client->bankAccount)->iban;

            return [
                'number'   => self::movementNumber($m),
                'date'     => $m->created_at,
                'currency' => $m->currency,
                'client'   => $client,
                'lines'    => [[
                    'description' => $m->kindLabel($loc) . ($m->counterparty ? ' — ' . $m->counterparty : ''),
                    'quantity'    => 1,
                    'unit'        => $m->amount,
                    'total'       => $m->amount,
                ]],
                'total'    => $m->amount,
                'sign'     => $credit ? '+' : '−',
                'tone'     => $credit ? 'ok' : 'bad',
                'badge'    => __($credit ? 'app.mv_credit_label' : 'app.mv_debit_label'),
                'kind'     => $m->kindLabel($loc),
                'rows'     => array_values(array_filter([
                    [__('movement.label_type'), $m->kindLabel($loc)],
                    $m->counterparty ? [$party, $m->counterparty] : null,
                    $m->counterparty_iban ? [__('movement.label_iban'), Invoice::formatIban($m->counterparty_iban)] : null,
                    $card ? [__('movement.label_card'), '•••• ' . $card->last_four] : null,
                    $m->reference ? [__('movement.label_reference'), $m->reference] : null,
                    $m->note ? [__('movement.label_note'), $m->note] : null,
                    [__('movement.label_date'), $m->created_at->format('d/m/Y H:i')],
                ])),
                'account'  => array_values(array_filter([
                    $iban ? [__('invoice.pay_iban'), Invoice::formatIban((string) $iban)] : null,
                    [__('movement.label_balance'), number_format($m->balance_after, 2, ',', ' ') . ' ' . $m->currency],
                ])),
            ];
        }
    }

    public static function forTransfer(Transfer $t): string
    {
        $t->loadMissing('user');

        return self::render($t->user, fn (string $loc) => self::transferDoc($t, $loc));
    }

    /** Données du document pour un virement (langue courante). */
    public static function transferDoc(Transfer $t, string $loc): array
    {
        $client = $t->user;

        {
            $send = $t->type === 'send';
            $iban = optional($client->bankAccount)->iban;

            return [
                'number'   => $t->reference,
                'date'     => $t->processed_at ?? $t->created_at,
                'currency' => $t->currency,
                'client'   => $client,
                'lines'    => [[
                    'description' => __($send ? 'transfer.type_send' : 'transfer.type_receive') . ' — ' . $t->beneficiary_name,
                    'quantity'    => 1,
                    'unit'        => $t->amount,
                    'total'       => $t->amount,
                ]],
                'total'    => $t->amount,
                'sign'     => $t->status === Transfer::STATUS_COMPLETED ? ($send ? '−' : '+') : '',
                'tone'     => match ($t->status) {
                    Transfer::STATUS_COMPLETED => 'ok',
                    Transfer::STATUS_REJECTED  => 'bad',
                    default                    => 'wait',
                },
                'badge'    => __('transfer.status_' . $t->status),
                'kind'     => __($send ? 'transfer.type_send' : 'transfer.type_receive'),
                'rows'     => array_values(array_filter([
                    [__('transfer.reference'), $t->reference],
                    [__('transfer.type'), __($send ? 'transfer.type_send' : 'transfer.type_receive')],
                    [__('transfer.beneficiary'), $t->beneficiary_name],
                    $t->beneficiary_iban ? [__('movement.label_iban'), Invoice::formatIban((string) $t->beneficiary_iban)] : null,
                    [__('transfer.created_at'), $t->created_at->format('d/m/Y H:i')],
                    $t->processed_at ? [__('transfer.processed_at'), $t->processed_at->format('d/m/Y H:i')] : null,
                    $t->status === Transfer::STATUS_REJECTED && $t->admin_note ? [__('transfer.reject_reason'), $t->admin_note] : null,
                    $t->note ? [__('transfer.note'), $t->note] : null,
                ])),
                'account'  => array_values(array_filter([
                    $iban ? [__('invoice.pay_iban'), Invoice::formatIban((string) $iban)] : null,
                ])),
            ];
        }
    }

    public static function movementFilename(AccountMovement $m): string
    {
        return 'Invoice-' . self::movementNumber($m) . '.pdf';
    }

    public static function transferFilename(Transfer $t): string
    {
        return 'Invoice-' . $t->reference . '.pdf';
    }

    private static function movementNumber(AccountMovement $m): string
    {
        return 'OP-' . str_pad((string) $m->id, 6, '0', STR_PAD_LEFT);
    }

    /** @param callable(string): array $build construit les données du document, dans la langue du client */
    private static function render(User $client, callable $build): string
    {
        $previous = app()->getLocale();
        $locale   = $client->locale ?: 'fr';
        app()->setLocale($locale);

        try {
            return Pdf::loadView('pdf.operation', ['doc' => $build($locale)])->setOption('enable_font_subsetting', true)->setPaper('a4')->output();
        } finally {
            app()->setLocale($previous);
        }
    }
}
