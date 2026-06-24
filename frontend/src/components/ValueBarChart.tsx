import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell } from 'recharts';
import { ValueResult } from '@/types';

interface Props { values: ValueResult[]; }

function scoreColor(score: number) {
  if (score > 8.9) return '#059669'; // CC — green
  if (score > 6.5) return '#ca8a04'; // GW — yellow
  if (score > 5.0) return '#ea580c'; // HDU — orange
  return '#dc2626';                  // ICU — red
}

export default function ValueBarChart({ values }: Props) {
  const data = values.map((v) => ({
    name: v.value_name.length > 14 ? v.value_name.slice(0, 14) + '…' : v.value_name,
    score: v.avg_score,
    gap: v.gap_percentage,
    fullName: v.value_name,
  }));

  return (
    <ResponsiveContainer width="100%" height={320}>
      <BarChart data={data} layout="vertical" margin={{ left: 8, right: 40, top: 4, bottom: 4 }}>
        <CartesianGrid strokeDasharray="3 3" horizontal={false} />
        <XAxis type="number" domain={[0, 10]} tickCount={11} tick={{ fontSize: 11 }} />
        <YAxis type="category" dataKey="name" width={120} tick={{ fontSize: 11 }} />
        <Tooltip
          formatter={(v: number, _: string, props: { payload?: { gap: number; fullName: string } }) => [
            `${v}/10  (${props.payload?.gap?.toFixed(1)}% gap)`,
            props.payload?.fullName,
          ]}
        />
        <Bar dataKey="score" radius={[0, 4, 4, 0]} label={{ position: 'right', formatter: (v: number) => `${v}`, fontSize: 11 }}>
          {data.map((entry, idx) => (
            <Cell key={idx} fill={scoreColor(entry.score)} />
          ))}
        </Bar>
      </BarChart>
    </ResponsiveContainer>
  );
}
