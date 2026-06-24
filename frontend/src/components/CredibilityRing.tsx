interface Props {
  score: number;
  band: string;   // 'icu' | 'hdu' | 'gw' | 'cc'
  size?: number;
}

const CLC: Record<string, { color: string; short: string; label: string }> = {
  cc:  { color: '#059669', short: 'CC',  label: 'Celebrity Credibility'  },
  gw:  { color: '#ca8a04', short: 'GW',  label: 'General Ward'           },
  hdu: { color: '#ea580c', short: 'HDU', label: 'High Dependency Unit'   },
  icu: { color: '#dc2626', short: 'ICU', label: 'Intensive Care Unit'     },
};

function getClc(band: string, score: number) {
  if (CLC[band]) return CLC[band];
  // Fallback: derive from score when band is an old/unknown value
  if (score > 89) return CLC.cc;
  if (score > 65) return CLC.gw;
  if (score > 50) return CLC.hdu;
  return CLC.icu;
}

export default function CredibilityRing({ score, band, size = 180 }: Props) {
  const r = (size / 2) - 16;
  const circumference = 2 * Math.PI * r;
  const fill = (score / 100) * circumference;
  const { color, short, label } = getClc(band, score);

  return (
    <div className="flex flex-col items-center gap-3">
      <svg width={size} height={size} style={{ transform: 'rotate(-90deg)' }}>
        <circle
          cx={size / 2} cy={size / 2} r={r}
          fill="none" stroke="#E5E7EB" strokeWidth="14"
        />
        <circle
          cx={size / 2} cy={size / 2} r={r}
          fill="none" stroke={color} strokeWidth="14"
          strokeDasharray={`${fill} ${circumference}`}
          strokeLinecap="round"
          style={{ transition: 'stroke-dasharray 0.8s ease' }}
        />
        {/* Score */}
        <text
          x="50%" y="45%"
          textAnchor="middle" dominantBaseline="middle"
          style={{ transform: 'rotate(90deg)', transformOrigin: 'center', fontSize: size * 0.2, fontWeight: 800, fill: color }}
        >
          {score.toFixed(1)}%
        </text>
        {/* CLC short code */}
        <text
          x="50%" y="63%"
          textAnchor="middle" dominantBaseline="middle"
          style={{ transform: 'rotate(90deg)', transformOrigin: 'center', fontSize: size * 0.11, fontWeight: 700, fill: color }}
        >
          {short}
        </text>
      </svg>
      {/* Stage label below ring */}
      <div className="text-center">
        <p className="text-sm font-medium text-gray-600">Overall Credibility Score</p>
        <p
          className="text-xs font-bold mt-0.5 px-3 py-0.5 rounded-full inline-block"
          style={{ color, background: `${color}18` }}
        >
          {label}
        </p>
      </div>
    </div>
  );
}
