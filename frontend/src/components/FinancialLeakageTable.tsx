import { ValueResult } from '@/types';
import clsx from 'clsx';

interface Props { values: ValueResult[]; totalLoss: number; }

export default function FinancialLeakageTable({ values, totalLoss }: Props) {
  const sorted = [...values].sort((a, b) => b.financial_loss - a.financial_loss);
  const topIds = new Set(sorted.slice(0, 3).map((v) => v.value_id));

  return (
    <div className="overflow-x-auto">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-brand-500 text-white text-left">
            <th className="px-4 py-3 font-semibold rounded-tl-lg">Value</th>
            <th className="px-4 py-3 font-semibold text-right">Financial Weight</th>
            <th className="px-4 py-3 font-semibold text-right">Gap %</th>
            <th className="px-4 py-3 font-semibold text-right rounded-tr-lg">$ Leakage</th>
          </tr>
        </thead>
        <tbody>
          {sorted.map((v, idx) => (
            <tr
              key={v.value_id}
              className={clsx(
                'border-b border-gray-100 transition-colors',
                topIds.has(v.value_id) && 'bg-red-50',
                !topIds.has(v.value_id) && idx % 2 === 0 ? 'bg-white' : 'bg-gray-50'
              )}
            >
              <td className="px-4 py-3 font-medium">
                {topIds.has(v.value_id) && (
                  <span className="inline-block w-2 h-2 bg-red-500 rounded-full mr-2" />
                )}
                {v.value_name}
              </td>
              <td className="px-4 py-3 text-right text-gray-600">
                ${v.financial_weight.toLocaleString()}
              </td>
              <td className="px-4 py-3 text-right">
                <span className={clsx(
                  'font-semibold',
                  v.gap_percentage >= 60 ? 'text-red-600' :
                  v.gap_percentage >= 40 ? 'text-amber-600' : 'text-green-600'
                )}>
                  {v.gap_percentage.toFixed(1)}%
                </span>
              </td>
              <td className="px-4 py-3 text-right font-semibold text-red-600">
                ${v.financial_loss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
              </td>
            </tr>
          ))}
          <tr className="bg-amber-50 border-t-2 border-amber-400 font-bold">
            <td className="px-4 py-3" colSpan={3}>Total Financial Leakage at Risk</td>
            <td className="px-4 py-3 text-right text-red-700 text-base">
              ${totalLoss.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
            </td>
          </tr>
        </tbody>
      </table>
      <p className="text-xs text-gray-400 mt-2">
        <span className="inline-block w-2 h-2 bg-red-500 rounded-full mr-1" />
        Top 3 costliest gaps highlighted
      </p>
    </div>
  );
}
